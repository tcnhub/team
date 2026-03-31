<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\TourPriceGroupRow;
use App\Models\TourPricePersonRow;
use App\Models\TourPriceSection;
use App\Models\TourPriceSimpleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TourPricingController extends Controller
{
    public function panel(Tour $tour)
    {
        return response()->json([
            'ok' => true,
            'html' => view('admin.tours.partials.pricing-panel', [
                'tour' => $this->loadPricingRelations($tour),
            ])->render(),
        ]);
    }

    public function storeSection(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'tipo' => ['required', Rule::in(['simple', 'por_persona', 'por_grupo'])],
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'anio' => ['nullable', 'integer', 'digits:4', 'min:2020', 'max:2100'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $tour->priceSections()->create([
            ...$validated,
            'orden' => $validated['orden'] ?? 0,
        ]);

        return $this->panel($tour->fresh());
    }

    public function updateSection(Request $request, Tour $tour, TourPriceSection $section)
    {
        abort_unless((int) $section->tour_id === (int) $tour->id, 404);

        $validated = $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'anio' => ['nullable', 'integer', 'digits:4', 'min:2020', 'max:2100'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $section->update([
            ...$validated,
            'orden' => $validated['orden'] ?? $section->orden,
        ]);

        return $this->panel($tour->fresh());
    }

    public function syncSection(Request $request, Tour $tour, TourPriceSection $section)
    {
        abort_unless((int) $section->tour_id === (int) $tour->id, 404);

        $validated = $request->validate($this->sectionSyncRules($section->tipo));

        DB::transaction(function () use ($section, $validated) {
            $section->update([
                'titulo' => $validated['titulo'],
                'descripcion' => $validated['descripcion'] ?? null,
                'anio' => $validated['anio'] ?? null,
                'orden' => $validated['orden'] ?? $section->orden,
            ]);

            $items = $validated['items'] ?? [];

            foreach ($items as $itemId => $attributes) {
                $model = $this->resolveItemModel($section->tipo, $section, (int) $itemId);

                $model->update(match ($section->tipo) {
                    'simple' => [
                        'descripcion' => $attributes['descripcion'] ?? null,
                        'precio_por_persona' => $attributes['precio_por_persona'],
                        'orden' => $attributes['orden'] ?? $model->orden,
                    ],
                    'por_persona' => [
                        'etiqueta_personas' => $attributes['etiqueta_personas'],
                        'descripcion' => $attributes['descripcion'] ?? null,
                        'precio_por_persona' => $attributes['precio_por_persona'],
                        'orden' => $attributes['orden'] ?? $model->orden,
                    ],
                    'por_grupo' => [
                        'etiqueta_grupo' => $attributes['etiqueta_grupo'],
                        'descripcion' => $attributes['descripcion'] ?? null,
                        'precio_por_grupo' => $attributes['precio_por_grupo'],
                        'orden' => $attributes['orden'] ?? $model->orden,
                    ],
                });
            }
        });

        return $this->panel($tour->fresh());
    }

    public function destroySection(Tour $tour, TourPriceSection $section)
    {
        abort_unless((int) $section->tour_id === (int) $tour->id, 404);

        $section->delete();

        return $this->panel($tour->fresh());
    }

    public function storeItem(Request $request, Tour $tour, TourPriceSection $section)
    {
        abort_unless((int) $section->tour_id === (int) $tour->id, 404);

        $validated = $request->validate($this->itemRules($section->tipo));
        $model = $this->itemModelClass($section->tipo);

        $model::create([
            ...$validated,
            'section_id' => $section->id,
            'orden' => $validated['orden'] ?? 0,
        ]);

        return $this->panel($tour->fresh());
    }

    public function updateItem(Request $request, Tour $tour, TourPriceSection $section, int $item)
    {
        abort_unless((int) $section->tour_id === (int) $tour->id, 404);

        $validated = $request->validate($this->itemRules($section->tipo));
        $model = $this->resolveItemModel($section->tipo, $section, $item);

        $model->update([
            ...$validated,
            'orden' => $validated['orden'] ?? $model->orden,
        ]);

        return $this->panel($tour->fresh());
    }

    public function destroyItem(Tour $tour, TourPriceSection $section, int $item)
    {
        abort_unless((int) $section->tour_id === (int) $tour->id, 404);

        $model = $this->resolveItemModel($section->tipo, $section, $item);
        $model->delete();

        return $this->panel($tour->fresh());
    }

    private function loadPricingRelations(Tour $tour): Tour
    {
        return $tour->load([
            'priceSections' => fn ($query) => $query->orderByRaw('anio is null desc')->orderBy('anio')->orderBy('orden')->orderBy('id'),
            'priceSections.simpleItems',
            'priceSections.personRows',
            'priceSections.groupRows',
        ]);
    }

    private function itemRules(string $tipo): array
    {
        return match ($tipo) {
            'simple' => [
                'descripcion' => ['nullable', 'string'],
                'precio_por_persona' => ['required', 'numeric', 'min:0'],
                'orden' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ],
            'por_persona' => [
                'etiqueta_personas' => ['required', 'string', 'max:100'],
                'descripcion' => ['nullable', 'string'],
                'precio_por_persona' => ['required', 'numeric', 'min:0'],
                'orden' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ],
            'por_grupo' => [
                'etiqueta_grupo' => ['required', 'string', 'max:150'],
                'descripcion' => ['nullable', 'string'],
                'precio_por_grupo' => ['required', 'numeric', 'min:0'],
                'orden' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ],
        };
    }

    private function sectionSyncRules(string $tipo): array
    {
        $baseRules = [
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'anio' => ['nullable', 'integer', 'digits:4', 'min:2020', 'max:2100'],
            'orden' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'items' => ['nullable', 'array'],
        ];

        return $baseRules + match ($tipo) {
            'simple' => [
                'items.*.descripcion' => ['nullable', 'string'],
                'items.*.precio_por_persona' => ['required', 'numeric', 'min:0'],
                'items.*.orden' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ],
            'por_persona' => [
                'items.*.etiqueta_personas' => ['required', 'string', 'max:100'],
                'items.*.descripcion' => ['nullable', 'string'],
                'items.*.precio_por_persona' => ['required', 'numeric', 'min:0'],
                'items.*.orden' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ],
            'por_grupo' => [
                'items.*.etiqueta_grupo' => ['required', 'string', 'max:150'],
                'items.*.descripcion' => ['nullable', 'string'],
                'items.*.precio_por_grupo' => ['required', 'numeric', 'min:0'],
                'items.*.orden' => ['nullable', 'integer', 'min:0', 'max:9999'],
            ],
        };
    }

    private function itemModelClass(string $tipo): string
    {
        return match ($tipo) {
            'simple' => TourPriceSimpleItem::class,
            'por_persona' => TourPricePersonRow::class,
            'por_grupo' => TourPriceGroupRow::class,
        };
    }

    private function resolveItemModel(string $tipo, TourPriceSection $section, int $itemId): TourPriceSimpleItem|TourPricePersonRow|TourPriceGroupRow
    {
        $modelClass = $this->itemModelClass($tipo);
        $item = $modelClass::query()
            ->where('section_id', $section->id)
            ->findOrFail($itemId);

        return $item;
    }
}
