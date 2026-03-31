@php
    $typeLabels = [
        'simple' => 'Precio unico por persona',
        'por_persona' => 'Tabla por numero de personas',
        'por_grupo' => 'Tabla por rango de grupo',
    ];
@endphp

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="ri-money-dollar-circle-line me-1"></i>Modelo de precios
        </h5>
    </div>
    <div class="card-body">
        <div class="border rounded p-3 bg-light-subtle mb-4">
            <h6 class="mb-3">Agregar bloque de precios</h6>
            <form action="{{ route('admin.tours.pricing.sections.store', $tour) }}" method="POST" data-pricing-ajax="1" class="row g-3">
                @csrf
                <div class="col-md-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select" required>
                        <option value="simple">Precio unico por persona</option>
                        <option value="por_persona">Tabla por personas</option>
                        <option value="por_grupo">Tabla por grupo</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Titulo</label>
                    <input type="text" name="titulo" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ano</label>
                    <input type="number" name="anio" class="form-control" min="2020" max="2100" placeholder="Todos">
                </div>
                <div class="col-md-1">
                    <label class="form-label">Orden</label>
                    <input type="number" name="orden" class="form-control" min="0" value="0">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Descripcion</label>
                    <textarea name="descripcion" class="form-control" rows="2"></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm" data-loading-label="Creando...">
                        <span class="button-default"><i class="ri-add-line me-1"></i>Crear bloque</span>
                        <span class="button-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span>Creando...</span>
                    </button>
                </div>
            </form>
        </div>

        @forelse($tour->priceSections as $section)
            @php
                $items = match ($section->tipo) {
                    'simple' => $section->simpleItems,
                    'por_persona' => $section->personRows,
                    'por_grupo' => $section->groupRows,
                };
                $sectionFormId = 'section-form-' . $section->id;
            @endphp

            <div class="border rounded p-3 mb-4">
                <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-3">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h6 class="mb-0">{{ $section->titulo }}</h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                {{ $typeLabels[$section->tipo] ?? $section->tipo }}
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                {{ $section->anio ?: 'Todos los anos' }}
                            </span>
                        </div>
                        @if($section->descripcion)
                            <div class="text-muted small mt-1">{{ $section->descripcion }}</div>
                        @endif
                    </div>

                    <form action="{{ route('admin.tours.pricing.sections.destroy', [$tour, $section]) }}"
                          method="POST"
                          data-http-method="DELETE"
                          data-pricing-ajax="1"
                          data-confirm="Eliminar este bloque de precios?"
                          class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm" data-loading-label="Eliminando...">
                            <span class="button-default"><i class="ri-delete-bin-line me-1"></i>Eliminar bloque</span>
                            <span class="button-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span>Eliminando...</span>
                        </button>
                    </form>
                </div>

                <form id="{{ $sectionFormId }}"
                      action="{{ route('admin.tours.pricing.sections.sync', [$tour, $section]) }}"
                      method="POST"
                      data-http-method="PATCH"
                      data-pricing-ajax="1"
                      class="row g-3 align-items-end mb-3">
                    @csrf
                    <div class="col-md-4">
                        <label class="form-label">Titulo</label>
                        <input type="text" name="titulo" class="form-control" value="{{ $section->titulo }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ano</label>
                        <input type="number" name="anio" class="form-control" min="2020" max="2100" value="{{ $section->anio }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Orden</label>
                        <input type="number" name="orden" class="form-control" min="0" value="{{ $section->orden }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Descripcion</label>
                        <input type="text" name="descripcion" class="form-control" value="{{ $section->descripcion }}">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit"
                                class="btn btn-outline-primary btn-sm"
                                data-loading-label="Guardando...">
                            <span class="button-default"><i class="ri-save-line me-1"></i>Guardar cambios</span>
                            <span class="button-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span>Guardando...</span>
                            <span class="button-success d-none"><i class="ri-check-line me-1"></i>Guardado</span>
                        </button>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm align-middle mb-3">
                        <thead>
                        <tr>
                            <th width="44"></th>
                            @if($section->tipo === 'simple')
                                <th>Descripcion</th>
                                <th width="180">Precio por persona</th>
                            @elseif($section->tipo === 'por_persona')
                                <th>Etiqueta personas</th>
                                <th>Descripcion</th>
                                <th width="180">Precio por persona</th>
                            @else
                                <th>Etiqueta grupo</th>
                                <th>Descripcion</th>
                                <th width="180">Precio por grupo</th>
                            @endif
                            <th width="100">Orden</th>
                            <th width="130">Acciones</th>
                        </tr>
                        </thead>
                        <tbody data-sortable-rows="1">
                        @forelse($items as $item)
                            <tr draggable="true" data-sortable-row="1">
                                <td class="text-center text-muted align-middle drag-handle-cell" title="Arrastrar para ordenar">
                                    <span class="drag-handle d-inline-flex align-items-center justify-content-center">
                                        <i class="ri-draggable"></i>
                                    </span>
                                </td>
                                @if($section->tipo === 'simple')
                                    <td>
                                        <input type="text" name="items[{{ $item->id }}][descripcion]" class="form-control" value="{{ $item->descripcion }}" form="{{ $sectionFormId }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="items[{{ $item->id }}][precio_por_persona]" class="form-control" value="{{ $item->precio_por_persona }}" form="{{ $sectionFormId }}" required>
                                    </td>
                                @elseif($section->tipo === 'por_persona')
                                    <td>
                                        <input type="text" name="items[{{ $item->id }}][etiqueta_personas]" class="form-control" value="{{ $item->etiqueta_personas }}" form="{{ $sectionFormId }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $item->id }}][descripcion]" class="form-control" value="{{ $item->descripcion }}" form="{{ $sectionFormId }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="items[{{ $item->id }}][precio_por_persona]" class="form-control" value="{{ $item->precio_por_persona }}" form="{{ $sectionFormId }}" required>
                                    </td>
                                @else
                                    <td>
                                        <input type="text" name="items[{{ $item->id }}][etiqueta_grupo]" class="form-control" value="{{ $item->etiqueta_grupo }}" form="{{ $sectionFormId }}" required>
                                    </td>
                                    <td>
                                        <input type="text" name="items[{{ $item->id }}][descripcion]" class="form-control" value="{{ $item->descripcion }}" form="{{ $sectionFormId }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" min="0" name="items[{{ $item->id }}][precio_por_grupo]" class="form-control" value="{{ $item->precio_por_grupo }}" form="{{ $sectionFormId }}" required>
                                    </td>
                                @endif
                                <td>
                                    <input type="number" min="0" name="items[{{ $item->id }}][orden]" class="form-control" value="{{ $item->orden }}" form="{{ $sectionFormId }}">
                                </td>
                                <td>
                                    <form action="{{ route('admin.tours.pricing.items.destroy', [$tour, $section, $item->id]) }}"
                                          method="POST"
                                          data-http-method="DELETE"
                                          data-pricing-ajax="1"
                                          data-confirm="Eliminar esta fila de precio?"
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" data-loading-label="Eliminando...">
                                            <span class="button-default">Eliminar</span>
                                            <span class="button-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span>Eliminando...</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $section->tipo === 'simple' ? 5 : 6 }}" class="text-muted text-center py-3">
                                    No hay filas de precio en este bloque.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border rounded p-3 bg-body-tertiary">
                    <h6 class="mb-3">Agregar fila de precio</h6>
                    <form action="{{ route('admin.tours.pricing.items.store', [$tour, $section]) }}" method="POST" data-pricing-ajax="1" class="row g-3 align-items-end">
                        @csrf
                        @if($section->tipo === 'simple')
                            <div class="col-md-7">
                                <label class="form-label">Descripcion</label>
                                <input type="text" name="descripcion" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Precio por persona</label>
                                <input type="number" step="0.01" min="0" name="precio_por_persona" class="form-control" required>
                            </div>
                        @elseif($section->tipo === 'por_persona')
                            <div class="col-md-3">
                                <label class="form-label">Etiqueta personas</label>
                                <input type="text" name="etiqueta_personas" class="form-control" placeholder="Ej: 2 personas" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Descripcion</label>
                                <input type="text" name="descripcion" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Precio por persona</label>
                                <input type="number" step="0.01" min="0" name="precio_por_persona" class="form-control" required>
                            </div>
                        @else
                            <div class="col-md-3">
                                <label class="form-label">Etiqueta grupo</label>
                                <input type="text" name="etiqueta_grupo" class="form-control" placeholder="Ej: Grupo de 2 a 5" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Descripcion</label>
                                <input type="text" name="descripcion" class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Precio por grupo</label>
                                <input type="number" step="0.01" min="0" name="precio_por_grupo" class="form-control" required>
                            </div>
                        @endif
                        <div class="col-md-1">
                            <label class="form-label">Orden</label>
                            <input type="number" name="orden" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-sm w-100" data-loading-label="Agregando...">
                                <span class="button-default"><i class="ri-add-line me-1"></i>Agregar fila</span>
                                <span class="button-loading d-none"><span class="spinner-border spinner-border-sm me-1"></span>Agregando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">
                <i class="ri-money-dollar-circle-line fs-1 d-block mb-2"></i>
                Este tour todavia no tiene bloques de precios.
            </div>
        @endforelse
    </div>
</div>
