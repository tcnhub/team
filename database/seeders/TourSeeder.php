<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TourSeeder extends Seeder
{
    public function run(): void
    {
        $tours = [
            [
                'codigo_tour'       => 'MP-CLASSIC-001',
                'nombre_tour'       => 'Machu Picchu Clásico 2D/1N',
                'descripcion_corta' => 'El tour más popular a la ciudadela inca con tren Expedition.',
                'descripcion_larga' => 'Disfruta de un recorrido completo por Machu Picchu, Cusco y el Valle Sagrado.',
                'duracion_dias'     => 2,
                'duracion_noches'   => 1,
                'nivel_dificultad'  => 'Fácil',
                'precio_base'       => 450.00,
                'max_personas'      => 16,
                'min_personas'      => 2,
                'salida_desde'      => 'Cusco',
                'destino_principal' => 'Machu Picchu',
                'incluye'           => "Traslados, tren ida y vuelta, entrada a Machu Picchu, guía profesional, almuerzo",
                'no_incluye'        => "Hotel en Aguas Calientes, bebidas extras, propinas",
                'itinerario'        => json_encode(["Día 1: Cusco → Ollantaytambo → Aguas Calientes", "Día 2: Machu Picchu + retorno a Cusco"]),
                'galeria_imagenes'  => json_encode(['tours/mp1.jpg', 'tours/mp2.jpg']),
                'estado'            => 'Activo',
                'destacado'         => true,
            ],
            [
                'codigo_tour'       => 'SV-MP-002',
                'nombre_tour'       => 'Valle Sagrado + Machu Picchu 3D/2N',
                'descripcion_corta' => 'Explora Pisac, Ollantaytambo y la maravilla del mundo.',
                'descripcion_larga' => 'Tour completo por el Valle Sagrado de los Incas y Machu Picchu.',
                'duracion_dias'     => 3,
                'duracion_noches'   => 2,
                'nivel_dificultad'  => 'Moderado',
                'precio_base'       => 680.00,
                'max_personas'      => 14,
                'min_personas'      => 2,
                'salida_desde'      => 'Cusco',
                'destino_principal' => 'Valle Sagrado',
                'estado'            => 'Activo',
                'destacado'         => true,
            ],
            // ... (continúa con los 28 restantes)
        ];

        // Aquí van los otros 28 tours de forma más compacta
        $otrosTours = [

            ['codigo_tour'=>'INCA-TRAIL-003','nombre_tour'=>'Inca Trail Clásico 4D/3N','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Difícil','precio_base'=>850,'destino_principal'=>'Machu Picchu','estado'=>'Activo','destacado'=>true],
            ['codigo_tour'=>'RAINBOW-004','nombre_tour'=>'Montaña Arcoíris Vinicunca Full Day','duracion_dias'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>85,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'HUMANTAY-005','nombre_tour'=>'Laguna Humantay Full Day','duracion_dias'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>95,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'TITICACA-006','nombre_tour'=>'Lago Titicaca Uros y Taquile 2D/1N','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>320,'destino_principal'=>'Puno','estado'=>'Activo'],
            ['codigo_tour'=>'NAZCA-007','nombre_tour'=>'Sobrevuelo Líneas de Nazca','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>280,'destino_principal'=>'Nazca','estado'=>'Activo'],
            ['codigo_tour'=>'PARACAS-008','nombre_tour'=>'Islas Ballestas y Reserva Paracas','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>120,'destino_principal'=>'Paracas','estado'=>'Activo'],
            ['codigo_tour'=>'COLCA-009','nombre_tour'=>'Cañón del Colca 2D/1N','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>380,'destino_principal'=>'Arequipa','estado'=>'Activo'],
            ['codigo_tour'=>'AMAZON-010','nombre_tour'=>'Amazonas Puerto Maldonado 4D/3N','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Moderado','precio_base'=>720,'destino_principal'=>'Amazonas','estado'=>'Activo'],

            ['codigo_tour'=>'MP-PREMIUM-011','nombre_tour'=>'Machu Picchu Premium con Huayna Picchu','duracion_dias'=>2,'nivel_dificultad'=>'Moderado','precio_base'=>520,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'SACRED-012','nombre_tour'=>'Valle Sagrado Maras y Moray','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>110,'destino_principal'=>'Valle Sagrado','estado'=>'Activo'],
            ['codigo_tour'=>'LUX-MP-013','nombre_tour'=>'Machu Picchu Luxury Train Hiram Bingham','duracion_dias'=>2,'nivel_dificultad'=>'Fácil','precio_base'=>980,'destino_principal'=>'Machu Picchu','estado'=>'Activo','destacado'=>true],
            ['codigo_tour'=>'CUSCO-CITY-014','nombre_tour'=>'City Tour Cusco 4 Ruinas','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>65,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'CHOQ-015','nombre_tour'=>'Choquequirao Trek 4D/3N','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Difícil','precio_base'=>680,'destino_principal'=>'Cusco','estado'=>'Activo'],

            ['codigo_tour'=>'SALK-016','nombre_tour'=>'Salkantay Trek 5D/4N','duracion_dias'=>5,'duracion_noches'=>4,'nivel_dificultad'=>'Difícil','precio_base'=>520,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'SALK-017','nombre_tour'=>'Salkantay Trek 4D/3N','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Moderado','precio_base'=>450,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'LARES-018','nombre_tour'=>'Lares Trek 4D/3N','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Moderado','precio_base'=>420,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'INCA-019','nombre_tour'=>'Short Inca Trail 2D/1N','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>480,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'INCA-020','nombre_tour'=>'Inca Jungle Trek 4D/3N','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Moderado','precio_base'=>390,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],

            ['codigo_tour'=>'RAINBOW-021','nombre_tour'=>'Palccoyo Rainbow Mountain','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>75,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'AUSANG-022','nombre_tour'=>'Ausangate Trek 5D/4N','duracion_dias'=>5,'duracion_noches'=>4,'nivel_dificultad'=>'Difícil','precio_base'=>610,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'AUSANG-023','nombre_tour'=>'Ausangate Trek 7D/6N','duracion_dias'=>7,'duracion_noches'=>6,'nivel_dificultad'=>'Difícil','precio_base'=>780,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'MP-DAY-024','nombre_tour'=>'Machu Picchu Full Day Tren','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>320,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-CAR-025','nombre_tour'=>'Machu Picchu By Car 2D/1N','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>210,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],

            ['codigo_tour'=>'MP-CAR-026','nombre_tour'=>'Machu Picchu By Car 3D/2N','duracion_dias'=>3,'duracion_noches'=>2,'nivel_dificultad'=>'Moderado','precio_base'=>260,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-LUX-027','nombre_tour'=>'Machu Picchu Luxury 3D/2N','duracion_dias'=>3,'duracion_noches'=>2,'nivel_dificultad'=>'Fácil','precio_base'=>1200,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'CUSCO-028','nombre_tour'=>'Cusco City Tour Privado','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>120,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'CUSCO-029','nombre_tour'=>'Cusco Walking Tour','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>35,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'CUSCO-030','nombre_tour'=>'Tour Gastronómico Cusco','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>90,'destino_principal'=>'Cusco','estado'=>'Activo'],

            ['codigo_tour'=>'SACRED-031','nombre_tour'=>'Valle Sagrado VIP','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>150,'destino_principal'=>'Valle Sagrado','estado'=>'Activo'],
            ['codigo_tour'=>'SACRED-032','nombre_tour'=>'Valle Sagrado 2D/1N','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>260,'destino_principal'=>'Valle Sagrado','estado'=>'Activo'],
            ['codigo_tour'=>'SACRED-033','nombre_tour'=>'Pisac y Ollantaytambo','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>95,'destino_principal'=>'Valle Sagrado','estado'=>'Activo'],
            ['codigo_tour'=>'SACRED-034','nombre_tour'=>'Moray y Salineras Maras','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>70,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'SACRED-035','nombre_tour'=>'Chinchero Cultural Tour','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>60,'destino_principal'=>'Cusco','estado'=>'Activo'],

            ['codigo_tour'=>'JUNGLE-036','nombre_tour'=>'Amazonas 3D/2N','duracion_dias'=>3,'duracion_noches'=>2,'nivel_dificultad'=>'Fácil','precio_base'=>420,'destino_principal'=>'Amazonas','estado'=>'Activo'],
            ['codigo_tour'=>'JUNGLE-037','nombre_tour'=>'Amazonas 5D/4N','duracion_dias'=>5,'duracion_noches'=>4,'nivel_dificultad'=>'Fácil','precio_base'=>820,'destino_principal'=>'Amazonas','estado'=>'Activo'],
            ['codigo_tour'=>'JUNGLE-038','nombre_tour'=>'Tambopata Explorer','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Fácil','precio_base'=>760,'destino_principal'=>'Amazonas','estado'=>'Activo'],
            ['codigo_tour'=>'JUNGLE-039','nombre_tour'=>'Tambopata Luxury Lodge','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Fácil','precio_base'=>1100,'destino_principal'=>'Amazonas','estado'=>'Activo'],
            ['codigo_tour'=>'JUNGLE-040','nombre_tour'=>'Manu National Park 5D','duracion_dias'=>5,'duracion_noches'=>4,'nivel_dificultad'=>'Moderado','precio_base'=>980,'destino_principal'=>'Amazonas','estado'=>'Activo'],

            ['codigo_tour'=>'AREQ-041','nombre_tour'=>'Arequipa City Tour','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>55,'destino_principal'=>'Arequipa','estado'=>'Activo'],
            ['codigo_tour'=>'AREQ-042','nombre_tour'=>'Cañón del Colca Full Day','duracion_dias'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>95,'destino_principal'=>'Arequipa','estado'=>'Activo'],
            ['codigo_tour'=>'AREQ-043','nombre_tour'=>'Colca Trek 3D/2N','duracion_dias'=>3,'duracion_noches'=>2,'nivel_dificultad'=>'Moderado','precio_base'=>390,'destino_principal'=>'Arequipa','estado'=>'Activo'],
            ['codigo_tour'=>'AREQ-044','nombre_tour'=>'Volcán Misti Trek','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Difícil','precio_base'=>450,'destino_principal'=>'Arequipa','estado'=>'Activo'],
            ['codigo_tour'=>'AREQ-045','nombre_tour'=>'Laguna Salinas','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>70,'destino_principal'=>'Arequipa','estado'=>'Activo'],

            ['codigo_tour'=>'PUNO-046','nombre_tour'=>'Uros Half Day','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>45,'destino_principal'=>'Puno','estado'=>'Activo'],
            ['codigo_tour'=>'PUNO-047','nombre_tour'=>'Uros y Taquile Full Day','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>75,'destino_principal'=>'Puno','estado'=>'Activo'],
            ['codigo_tour'=>'PUNO-048','nombre_tour'=>'Amantani 2D/1N','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>140,'destino_principal'=>'Puno','estado'=>'Activo'],
            ['codigo_tour'=>'PUNO-049','nombre_tour'=>'Ruta del Sol Cusco Puno','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>95,'destino_principal'=>'Puno','estado'=>'Activo'],
            ['codigo_tour'=>'PUNO-050','nombre_tour'=>'Titicaca Luxury Experience','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>450,'destino_principal'=>'Puno','estado'=>'Activo'],

            ['codigo_tour'=>'LIMA-051','nombre_tour'=>'Lima City Tour','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>70,'destino_principal'=>'Lima','estado'=>'Activo'],
            ['codigo_tour'=>'LIMA-052','nombre_tour'=>'Lima Food Tour','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>110,'destino_principal'=>'Lima','estado'=>'Activo'],
            ['codigo_tour'=>'LIMA-053','nombre_tour'=>'Barranco Walking Tour','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>45,'destino_principal'=>'Lima','estado'=>'Activo'],
            ['codigo_tour'=>'LIMA-054','nombre_tour'=>'Museos de Lima','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>60,'destino_principal'=>'Lima','estado'=>'Activo'],
            ['codigo_tour'=>'LIMA-055','nombre_tour'=>'Paracas y Huacachina 2D','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>240,'destino_principal'=>'Paracas','estado'=>'Activo'],

            ['codigo_tour'=>'ICA-056','nombre_tour'=>'Sandboarding Huacachina','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>65,'destino_principal'=>'Ica','estado'=>'Activo'],
            ['codigo_tour'=>'ICA-057','nombre_tour'=>'Buggies Huacachina','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>60,'destino_principal'=>'Ica','estado'=>'Activo'],
            ['codigo_tour'=>'ICA-058','nombre_tour'=>'Viñedos Ica Tour','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>75,'destino_principal'=>'Ica','estado'=>'Activo'],
            ['codigo_tour'=>'ICA-059','nombre_tour'=>'Nazca Lines Overflight','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>300,'destino_principal'=>'Nazca','estado'=>'Activo'],
            ['codigo_tour'=>'ICA-060','nombre_tour'=>'Nazca + Paracas 2D','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>380,'destino_principal'=>'Nazca','estado'=>'Activo'],

            ['codigo_tour'=>'ADVENT-061','nombre_tour'=>'Rafting Río Urubamba','duracion_dias'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>90,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'ADVENT-062','nombre_tour'=>'Zipline Valle Sagrado','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>85,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'ADVENT-063','nombre_tour'=>'ATV Maras Moray','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>70,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'ADVENT-064','nombre_tour'=>'ATV Rainbow Mountain','duracion_dias'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>120,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'ADVENT-065','nombre_tour'=>'Mountain Bike Cusco','duracion_dias'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>95,'destino_principal'=>'Cusco','estado'=>'Activo'],

            ['codigo_tour'=>'LUX-066','nombre_tour'=>'Luxury Peru 7D','duracion_dias'=>7,'duracion_noches'=>6,'nivel_dificultad'=>'Fácil','precio_base'=>3200,'destino_principal'=>'Perú','estado'=>'Activo','destacado'=>true],
            ['codigo_tour'=>'LUX-067','nombre_tour'=>'Luxury Cusco Experience','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Fácil','precio_base'=>1800,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'LUX-068','nombre_tour'=>'Luxury Sacred Valley','duracion_dias'=>3,'duracion_noches'=>2,'nivel_dificultad'=>'Fácil','precio_base'=>1500,'destino_principal'=>'Valle Sagrado','estado'=>'Activo'],
            ['codigo_tour'=>'LUX-069','nombre_tour'=>'Luxury Machu Picchu 2D','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>2100,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'LUX-070','nombre_tour'=>'Luxury Amazon','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Fácil','precio_base'=>2400,'destino_principal'=>'Amazonas','estado'=>'Activo'],

            ['codigo_tour'=>'EXP-071','nombre_tour'=>'Perú Express 5D','duracion_dias'=>5,'duracion_noches'=>4,'nivel_dificultad'=>'Fácil','precio_base'=>1200,'destino_principal'=>'Perú','estado'=>'Activo'],
            ['codigo_tour'=>'EXP-072','nombre_tour'=>'Cusco Explorer 4D','duracion_dias'=>4,'duracion_noches'=>3,'nivel_dificultad'=>'Fácil','precio_base'=>850,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'EXP-073','nombre_tour'=>'Andes Adventure 6D','duracion_dias'=>6,'duracion_noches'=>5,'nivel_dificultad'=>'Moderado','precio_base'=>1400,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'EXP-074','nombre_tour'=>'Perú Clásico 8D','duracion_dias'=>8,'duracion_noches'=>7,'nivel_dificultad'=>'Fácil','precio_base'=>2100,'destino_principal'=>'Perú','estado'=>'Activo'],
            ['codigo_tour'=>'EXP-075','nombre_tour'=>'Perú Completo 12D','duracion_dias'=>12,'duracion_noches'=>11,'nivel_dificultad'=>'Fácil','precio_base'=>3500,'destino_principal'=>'Perú','estado'=>'Activo'],

            ['codigo_tour'=>'TREK-076','nombre_tour'=>'Huchuy Qosqo Trek','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>240,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'TREK-077','nombre_tour'=>'Huchuy Qosqo Trek 3D','duracion_dias'=>3,'duracion_noches'=>2,'nivel_dificultad'=>'Moderado','precio_base'=>320,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'TREK-078','nombre_tour'=>'Vilcabamba Trek','duracion_dias'=>5,'duracion_noches'=>4,'nivel_dificultad'=>'Difícil','precio_base'=>720,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'TREK-079','nombre_tour'=>'Ancascocha Trek','duracion_dias'=>5,'duracion_noches'=>4,'nivel_dificultad'=>'Difícil','precio_base'=>680,'destino_principal'=>'Cusco','estado'=>'Activo'],
            ['codigo_tour'=>'TREK-080','nombre_tour'=>'Salkantay + Inca Trail','duracion_dias'=>7,'duracion_noches'=>6,'nivel_dificultad'=>'Difícil','precio_base'=>950,'destino_principal'=>'Cusco','estado'=>'Activo'],

            ['codigo_tour'=>'MP-081','nombre_tour'=>'Machu Picchu + Huayna Picchu','duracion_dias'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>380,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-082','nombre_tour'=>'Machu Picchu + Montaña','duracion_dias'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>370,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-083','nombre_tour'=>'Machu Picchu Sunrise','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>340,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-084','nombre_tour'=>'Machu Picchu Sunset','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>330,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-085','nombre_tour'=>'Machu Picchu Photography Tour','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>550,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],

            ['codigo_tour'=>'MP-086','nombre_tour'=>'Machu Picchu Trekking Experience','duracion_dias'=>3,'duracion_noches'=>2,'nivel_dificultad'=>'Moderado','precio_base'=>620,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-087','nombre_tour'=>'Machu Picchu Backpacker','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>190,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-088','nombre_tour'=>'Machu Picchu Budget','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>160,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-089','nombre_tour'=>'Machu Picchu Train Experience','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>350,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-090','nombre_tour'=>'Machu Picchu Expedition','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>310,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],

            ['codigo_tour'=>'MP-091','nombre_tour'=>'Machu Picchu Deluxe','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>750,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-092','nombre_tour'=>'Machu Picchu VIP','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>880,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-093','nombre_tour'=>'Machu Picchu Private Guide','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>420,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-094','nombre_tour'=>'Machu Picchu Group Tour','duracion_dias'=>1,'nivel_dificultad'=>'Fácil','precio_base'=>280,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],
            ['codigo_tour'=>'MP-095','nombre_tour'=>'Machu Picchu Explorer','duracion_dias'=>2,'duracion_noches'=>1,'nivel_dificultad'=>'Moderado','precio_base'=>420,'destino_principal'=>'Machu Picchu','estado'=>'Activo'],

            ['codigo_tour'=>'PERU-096','nombre_tour'=>'Perú Highlights 10D','duracion_dias'=>10,'duracion_noches'=>9,'nivel_dificultad'=>'Fácil','precio_base'=>2800,'destino_principal'=>'Perú','estado'=>'Activo'],
            ['codigo_tour'=>'PERU-097','nombre_tour'=>'Perú Cultural 7D','duracion_dias'=>7,'duracion_noches'=>6,'nivel_dificultad'=>'Fácil','precio_base'=>1800,'destino_principal'=>'Perú','estado'=>'Activo'],
            ['codigo_tour'=>'PERU-098','nombre_tour'=>'Perú Naturaleza 9D','duracion_dias'=>9,'duracion_noches'=>8,'nivel_dificultad'=>'Moderado','precio_base'=>2300,'destino_principal'=>'Perú','estado'=>'Activo'],
            ['codigo_tour'=>'PERU-099','nombre_tour'=>'Perú Andes 8D','duracion_dias'=>8,'duracion_noches'=>7,'nivel_dificultad'=>'Moderado','precio_base'=>2100,'destino_principal'=>'Perú','estado'=>'Activo'],
            ['codigo_tour'=>'PERU-100','nombre_tour'=>'Gran Tour Perú 15D','duracion_dias'=>15,'duracion_noches'=>14,'nivel_dificultad'=>'Fácil','precio_base'=>4200,'destino_principal'=>'Perú','estado'=>'Activo']

        ];

        // Combinamos todo
        $allTours = array_merge($tours, $otrosTours);

        // Insertamos los 30 tours
        foreach ($allTours as $index => $data) {
            // Completamos campos faltantes con valores por defecto
            Tour::create(array_merge([
                'descripcion_corta' => 'Tour increíble por Perú con guías expertos.',
                'descripcion_larga' => 'Disfruta de una experiencia única en uno de los destinos más impresionantes del mundo.',
                'precio_base'       => rand(80, 1200),
                'moneda'            => 'PEN',
                'max_personas'      => rand(8, 20),
                'min_personas'      => rand(1, 4),
                'salida_desde'      => 'Cusco o Lima',
                'destino_principal' => 'Perú',
                'estado'            => 'Activo',
                'destacado'         => $index < 8, // primeros 8 destacados
            ], $data));
        }

        $this->command->info('✅ Se han creado 30 tours exitosamente.');
    }
}
