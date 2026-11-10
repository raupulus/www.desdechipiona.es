<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Category;
use App\Models\OfficialPhone;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder de estructura lógica base de Desde Chipiona (categorías, grupos temáticos,
 * ajustes del portal y teléfonos públicos institucionales).
 *
 * IMPORTANTE: Este seeder contiene únicamente la estructura lógica y taxonomía pública
 * del proyecto (bajo licencia AGPLv3). El contenido de las fichas de investigación
 * privadas NO se almacena en el repositorio de código; se importa localmente desde
 * el directorio privado de investigación mediante el comando `php artisan chipiona:import-corpus`.
 */
class ChipionaStructureSeeder extends Seeder
{
    /**
     * Ejecuta la carga idempotente de categorías, ajustes y teléfonos públicos.
     */
    public function run(): void
    {
        $this->seedAuthorAndSettings();
        $this->seedCategories();
        $this->seedOfficialPhones();
    }

    /**
     * Asegura la cuenta de autoría y los ajustes corporativos de Desde Chipiona.
     */
    protected function seedAuthorAndSettings(): User
    {
        $author = User::updateOrCreate(
            ['email' => 'public@raupulus.dev'],
            [
                'name' => 'Raúl Caro Pastorino',
                'password' => Hash::make('password'),
                'role' => Role::SuperAdmin,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $settings = [
            ['key' => 'site_title', 'display_name' => 'Título del sitio', 'value' => 'Desde Chipiona', 'type' => 'text', 'group' => 'general', 'order' => 1],
            ['key' => 'site_description', 'display_name' => 'Descripción del sitio', 'value' => 'Portal documental, histórico, natural y cultural sobre Chipiona (Cádiz). Investigación personal e iniciativa privada de Raúl Caro Pastorino.', 'type' => 'textarea', 'group' => 'general', 'order' => 2],
            ['key' => 'contact_email', 'display_name' => 'Email de contacto', 'value' => 'info@desdechipiona.es', 'type' => 'text', 'group' => 'contact', 'order' => 1],
            ['key' => 'noreply_email', 'display_name' => 'Email de envíos automáticos', 'value' => 'no-reply@desdechipiona.es', 'type' => 'text', 'group' => 'contact', 'order' => 2],
            ['key' => 'contact_address', 'display_name' => 'Ubicación', 'value' => 'Chipiona (Cádiz), Andalucía, España', 'type' => 'textarea', 'group' => 'contact', 'order' => 3],
            ['key' => 'facebook_url', 'display_name' => 'Facebook', 'value' => 'https://www.facebook.com/desdechipiona', 'type' => 'text', 'group' => 'social', 'order' => 1],
            ['key' => 'github_url', 'display_name' => 'GitHub', 'value' => 'https://github.com/raupulus/www.desdechipiona.es', 'type' => 'text', 'group' => 'social', 'order' => 2],
            ['key' => 'gitlab_url', 'display_name' => 'GitLab', 'value' => 'https://gitlab.com/raupulus/www.desdechipiona.es', 'type' => 'text', 'group' => 'social', 'order' => 3],
            ['key' => 'telegram_url', 'display_name' => 'Canal de Telegram', 'value' => 'https://t.me/desdechipiona', 'type' => 'text', 'group' => 'social', 'order' => 4],
            ['key' => 'youtube_url', 'display_name' => 'YouTube', 'value' => 'https://www.youtube.com/@desdechipiona', 'type' => 'text', 'group' => 'social', 'order' => 5],
            ['key' => 'author_url', 'display_name' => 'Web del Autor', 'value' => 'https://raupulus.dev', 'type' => 'text', 'group' => 'social', 'order' => 6],
            ['key' => 'default_meta_keywords', 'display_name' => 'Keywords por defecto', 'value' => 'Chipiona, historia de Chipiona, Faro de Chipiona, Corrales de Pesca, Virgen de Regla, Camaleón, Moscatel de Chipiona, Raúl Caro Pastorino', 'type' => 'text', 'group' => 'seo', 'order' => 1],
            ['key' => 'adsense_enabled', 'display_name' => 'Google AdSense activo (1 = sí, 0 = no)', 'value' => '0', 'type' => 'text', 'group' => 'ads', 'order' => 1],
            ['key' => 'adsense_client_id', 'display_name' => 'Google AdSense Client ID (ca-pub-...)', 'value' => '', 'type' => 'text', 'group' => 'ads', 'order' => 2],
            ['key' => 'adsense_slot_sidebar', 'display_name' => 'Google AdSense Slot Lateral (300x250)', 'value' => '', 'type' => 'text', 'group' => 'ads', 'order' => 3],
            ['key' => 'adsense_slot_horizontal', 'display_name' => 'Google AdSense Slot Horizontal (728x90)', 'value' => '', 'type' => 'text', 'group' => 'ads', 'order' => 4],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        return $author;
    }

    /**
     * Crea o actualiza la taxonomía de categorías temáticas de Chipiona.
     *
     * @return array<string, Category>
     */
    public function seedCategories(): array
    {
        $definitions = [
            'historia' => [
                'name' => ['es' => 'Historia y Archivo', 'en' => 'History & Archive'],
                'description' => ['es' => 'Episodios históricos de Chipiona contrastados con documentos de archivo y arqueología.', 'en' => 'Historical episodes of Chipiona verified with archive documents and archaeology.'],
                'order' => 1,
            ],
            'monumentos' => [
                'name' => ['es' => 'Monumentos y Patrimonio', 'en' => 'Monuments & Heritage'],
                'description' => ['es' => 'Faro, Santuario de Regla, Castillo, templos y arquitectura civil y marítima.', 'en' => 'Lighthouse, Sanctuary of Regla, Castle, temples, and civil and maritime architecture.'],
                'order' => 2,
            ],
            'playas' => [
                'name' => ['es' => 'Playas y Corrales de Pesca', 'en' => 'Beaches & Fishing Corrals'],
                'description' => ['es' => 'Arenales atlánticos, restingas y los nueve corrales de pesca de piedra ostionera.', 'en' => 'Atlantic beaches, reefs, and the nine oyster-stone fishing corrals.'],
                'order' => 3,
            ],
            'fauna' => [
                'name' => ['es' => 'Fauna Protegida', 'en' => 'Protected Fauna'],
                'description' => ['es' => 'Camaleón común, chorlitejo patinegro, aves limícolas y vida marina intermareal.', 'en' => 'Common chameleon, Kentish plover, shorebirds, and intertidal marine life.'],
                'order' => 4,
            ],
            'flora' => [
                'name' => ['es' => 'Flora, Dunas y Pinares', 'en' => 'Flora, Dunes & Pine Forests'],
                'description' => ['es' => 'Cordón dunar, Pinar de la Villa, retama blanca, azucena de mar y endemismos botánicos.', 'en' => 'Dune belt, Pinar de la Villa, white broom, sea daffodil, and botanical endemisms.'],
                'order' => 5,
            ],
            'gastronomia' => [
                'name' => ['es' => 'Gastronomía y Vino Moscatel', 'en' => 'Gastronomy & Moscatel Wine'],
                'description' => ['es' => 'Vino moscatel, soleo de la pasa, guisos marineros, huerta de navazo y panadería tradicional.', 'en' => 'Moscatel wine, sun-dried raisins, seafood stews, navazo orchards, and traditional bakery.'],
                'order' => 6,
            ],
            'personajes' => [
                'name' => ['es' => 'Personajes y Memoria', 'en' => 'Personalities & Memory'],
                'description' => ['es' => 'Hijos predilectos, artistas, médicos humanistas e ingenieros que dejaron huella en Chipiona.', 'en' => 'Beloved citizens, artists, humanist physicians, and engineers who dejaron huella en Chipiona.'],
                'order' => 7,
            ],
            'fiestas' => [
                'name' => ['es' => 'Fiestas y Tradiciones', 'en' => 'Festivals & Traditions'],
                'description' => ['es' => 'Carnaval, Velada de Regla, Romería del Pinar, fiestas del Moscatel y votos históricos.', 'en' => 'Carnival, Velada de Regla, Romería del Pinar, Moscatel festivals, and historical vows.'],
                'order' => 8,
            ],
        ];

        $result = [];
        foreach ($definitions as $slug => $data) {
            $result[$slug] = Category::updateOrCreate(
                ['slug' => $slug],
                array_merge($data, ['is_active' => true])
            );
        }

        return $result;
    }

    /**
     * Siembra el directorio público de teléfonos de emergencias y servicios oficiales de Chipiona.
     */
    protected function seedOfficialPhones(): void
    {
        $phones = [
            [
                'name' => 'Emergencias Generales Andalucía (112)',
                'category' => 'emergencias',
                'phone' => '112',
                'secondary_phone' => null,
                'address' => 'Cobertura integral en todo el término municipal de Chipiona',
                'schedule' => '24 horas · 365 días',
                'description' => 'Teléfono único europeo de urgencias. Coordina Policía Local, Guardia Civil, Bomberos y Emergencias Sanitarias 061.',
                'latitude' => 36.7366,
                'longitude' => -6.4375,
                'is_emergency' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Policía Local de Chipiona',
                'category' => 'emergencias',
                'phone' => '956 92 23 40',
                'secondary_phone' => '092',
                'address' => 'Avenida del Esturión, 11 (Jefatura de Policía Local)',
                'schedule' => '24 horas · 365 días',
                'description' => 'Seguridad ciudadana, tráfico, atestados y vigilancia municipal.',
                'latitude' => 36.7332,
                'longitude' => -6.4310,
                'is_emergency' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Guardia Civil — Puesto Principal de Chipiona',
                'category' => 'emergencias',
                'phone' => '956 37 14 99',
                'secondary_phone' => '062',
                'address' => 'Calle Juanita Reina, s/n',
                'schedule' => '24 horas · 365 días',
                'description' => 'Seguridad ciudadana, denuncias, vigilancia costera y rural.',
                'latitude' => 36.7318,
                'longitude' => -6.4342,
                'is_emergency' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Centro de Salud Doctor Tolosa Latour (Urgencias y Cita)',
                'category' => 'salud',
                'phone' => '956 01 20 07',
                'secondary_phone' => '956 38 65 11',
                'address' => 'Avenida de la Diputación, s/n, Chipiona',
                'schedule' => 'Urgencias 24h · Consultas de lunes a viernes de 08:00 a 20:00',
                'description' => 'Atención primaria y urgencias del Servicio Andaluz de Salud (SAS). Cita previa: 955 54 50 60 y 061.',
                'latitude' => 36.7341,
                'longitude' => -6.4305,
                'is_emergency' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Protección Civil de Chipiona',
                'category' => 'emergencias',
                'phone' => '662 34 43 40',
                'secondary_phone' => '956 92 90 60',
                'address' => 'Delegación Municipal de Protección Civil y Playas',
                'schedule' => 'Operativo permanente en temporada de playas y eventos',
                'description' => 'Salvamento y socorrismo en playas, apoyo en eventos y alertas meteorológicas.',
                'latitude' => 36.7308,
                'longitude' => -6.4395,
                'is_emergency' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Cruz Roja Española — Asamblea Local de Chipiona',
                'category' => 'salud',
                'phone' => '956 37 42 22',
                'secondary_phone' => null,
                'address' => 'Avenida de la Cruz Roja Española, 35',
                'schedule' => 'Lunes a viernes de 09:00 a 14:00',
                'description' => 'Acción social, apoyo a personas mayores y socorros en el municipio.',
                'latitude' => 36.7348,
                'longitude' => -6.4358,
                'is_emergency' => false,
                'sort_order' => 6,
            ],
            [
                'name' => 'Consorcio de Bomberos de la Provincia de Cádiz',
                'category' => 'emergencias',
                'phone' => '085',
                'secondary_phone' => '112',
                'address' => 'Parque comarcal de referencia (Sanlúcar de Barrameda - Chipiona)',
                'schedule' => '24 horas · 365 días',
                'description' => 'Extinción de incendios urbanos, agrícolas y forestales y rescate en accidentes.',
                'latitude' => 36.7680,
                'longitude' => -6.3530,
                'is_emergency' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Ayuntamiento de Chipiona (Centralita y Registro General)',
                'category' => 'ayuntamiento',
                'phone' => '956 92 90 60',
                'secondary_phone' => null,
                'address' => 'Plaza de Andalucía, s/n, 11550 Chipiona',
                'schedule' => 'Lunes a viernes de 09:00 a 14:00',
                'description' => 'Atención ciudadana general, Registro de Entrada, Alcaldía y delegaciones municipales.',
                'latitude' => 36.7368,
                'longitude' => -6.4369,
                'is_emergency' => false,
                'sort_order' => 10,
            ],
            [
                'name' => 'Delegación Municipal de Playas y Medio Ambiente',
                'category' => 'ayuntamiento',
                'phone' => '956 92 23 67',
                'secondary_phone' => '600 49 31 36',
                'address' => 'Paseo Costa de la Luz / Edificio Municipal',
                'schedule' => 'Lunes a viernes de 09:00 a 14:00',
                'description' => 'Gestión de servicios de playa, baños adaptados y medio ambiente.',
                'latitude' => 36.7305,
                'longitude' => -6.4398,
                'is_emergency' => false,
                'sort_order' => 11,
            ],
            [
                'name' => 'Servicios Sociales y Tercera Edad (Bienestar Social)',
                'category' => 'ayuntamiento',
                'phone' => '956 92 90 66',
                'secondary_phone' => '662 31 99 60',
                'address' => 'Centro Municipal de Servicios Sociales, Chipiona',
                'schedule' => 'Lunes a viernes de 09:00 a 14:00',
                'description' => 'Atención social comunitaria, políticas del mayor, dependencia e igualdad (CIM).',
                'latitude' => 36.7355,
                'longitude' => -6.4345,
                'is_emergency' => false,
                'sort_order' => 12,
            ],
            [
                'name' => 'Oficina Municipal de Turismo y Castillo de Chipiona',
                'category' => 'cultura',
                'phone' => '956 92 90 65',
                'secondary_phone' => null,
                'address' => 'Calle Castillo, 5 (Castillo de Chipiona)',
                'schedule' => 'Lunes a domingo de 10:00 a 14:00',
                'description' => 'Información turística oficial, reservas para el Faro y Centro de Interpretación «Cádiz y el Nuevo Mundo».',
                'latitude' => 36.7398,
                'longitude' => -6.4391,
                'is_emergency' => false,
                'sort_order' => 15,
            ],
            [
                'name' => 'Centro de Interpretación de la Naturaleza «El Camaleón»',
                'category' => 'cultura',
                'phone' => '956 92 90 63',
                'secondary_phone' => null,
                'address' => 'Avenida de la Laguna, s/n (junto a la Playa de Camarón)',
                'schedule' => 'Martes a domingo de 10:00 a 14:00',
                'description' => 'Aula ambiental sobre el camaleón común, los corrales de pesca y el cordón dunar.',
                'latitude' => 36.7165,
                'longitude' => -6.4312,
                'is_emergency' => false,
                'sort_order' => 16,
            ],
            [
                'name' => 'Archivo Histórico y Biblioteca Pública Municipal',
                'category' => 'cultura',
                'phone' => '956 92 23 79',
                'secondary_phone' => '662 32 99 59',
                'address' => 'Calle Larga, 74 / Dependencias de Archivo Municipal',
                'schedule' => 'Lunes a viernes de 09:00 a 14:00 y de 16:30 a 20:30',
                'description' => 'Consulta bibliográfica, hemeroteca local y fondo documental del Ayuntamiento de Chipiona.',
                'latitude' => 36.7362,
                'longitude' => -6.4361,
                'is_emergency' => false,
                'sort_order' => 17,
            ],
            [
                'name' => 'Santuario de Nuestra Señora de Regla',
                'category' => 'cultura',
                'phone' => '956 37 09 89',
                'secondary_phone' => '956 37 01 89',
                'address' => 'Paseo Costa de la Luz, 91 (Plaza de Nuestra Señora de Regla)',
                'schedule' => 'Abierto todos los días en horario de culto y visitas',
                'description' => 'Comunidad franciscana, horario de misas y visitas al claustro y camarín.',
                'latitude' => 36.7302,
                'longitude' => -6.4401,
                'is_emergency' => false,
                'sort_order' => 18,
            ],
            [
                'name' => 'Museo del Moscatel (Cooperativa Católico Agrícola)',
                'category' => 'cultura',
                'phone' => '956 37 18 04',
                'secondary_phone' => '956 37 15 94',
                'address' => 'Avenida de Regla, 8-10, Chipiona',
                'schedule' => 'Martes a domingo con pases guiados',
                'description' => 'Museo vitivinícola sobre el vino moscatel, el soleo de la pasa y las bodegas tradicionales.',
                'latitude' => 36.7351,
                'longitude' => -6.4330,
                'is_emergency' => false,
                'sort_order' => 19,
            ],
            [
                'name' => 'Centro de Interpretación Rocío Jurado («La Voz del Milenio»)',
                'category' => 'cultura',
                'phone' => '685 93 51 79',
                'secondary_phone' => '685 93 51 88',
                'address' => 'Avenida de Granada, 37 (Complejo Cultural El Pozo)',
                'schedule' => 'Martes a domingo de 10:00 a 14:00 y de 17:00 a 20:00',
                'description' => 'Espacio museístico municipal dedicado a la trayectoria artística de Rocío Jurado.',
                'latitude' => 36.7324,
                'longitude' => -6.4278,
                'is_emergency' => false,
                'sort_order' => 20,
            ],
            [
                'name' => 'Puerto Deportivo y Pesquero de Chipiona (APPA / Lonja)',
                'category' => 'transporte',
                'phone' => '856 10 97 11',
                'secondary_phone' => '956 37 60 91',
                'address' => 'Avenida de Rocío Jurado, s/n (Puerto de Chipiona)',
                'schedule' => 'Oficina de 09:00 a 14:00 · Marinería 24h (VHF Canal 9)',
                'description' => 'Agencia Pública de Puertos de Andalucía y Lonja Pesquera de Chipiona.',
                'latitude' => 36.7429,
                'longitude' => -6.4334,
                'is_emergency' => false,
                'sort_order' => 25,
            ],
            [
                'name' => 'Radio Televisión Municipal (RTV Chipiona — 107.1 FM)',
                'category' => 'cultura',
                'phone' => '956 92 23 82',
                'secondary_phone' => null,
                'address' => 'Estudios Municipales de Radio y Televisión, Chipiona',
                'schedule' => 'Emisión continua en 107.1 FM',
                'description' => 'Emisora pública municipal decana de la información local.',
                'latitude' => 36.7365,
                'longitude' => -6.4365,
                'is_emergency' => false,
                'sort_order' => 26,
            ],
        ];

        foreach ($phones as $phoneData) {
            OfficialPhone::updateOrCreate(
                ['name' => $phoneData['name']],
                array_merge($phoneData, ['is_active' => true])
            );
        }
    }
}
