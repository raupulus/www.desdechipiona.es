<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $s = [
            ['key' => 'site_title', 'display_name' => 'Título del sitio', 'value' => 'Desde Chipiona', 'type' => 'text', 'group' => 'general', 'order' => 1],
            ['key' => 'site_description', 'display_name' => 'Descripción del sitio', 'value' => 'Portal documental, histórico, natural y cultural sobre Chipiona (Cádiz). Investigación personal e iniciativa privada de Raúl Caro Pastorino.', 'type' => 'textarea', 'group' => 'general', 'order' => 2],
            ['key' => 'contact_email', 'display_name' => 'Email de contacto', 'value' => 'info@desdechipiona.es', 'type' => 'text', 'group' => 'contact', 'order' => 1],
            ['key' => 'noreply_email', 'display_name' => 'Email de envíos automáticos', 'value' => 'no-reply@desdechipiona.es', 'type' => 'text', 'group' => 'contact', 'order' => 2],
            ['key' => 'contact_address', 'display_name' => 'Ubicación', 'value' => 'Chipiona (Cádiz), Andalucía, España', 'type' => 'textarea', 'group' => 'contact', 'order' => 3],
            ['key' => 'facebook_url', 'display_name' => 'Facebook', 'value' => 'https://www.facebook.com/desdechipiona', 'type' => 'text', 'group' => 'social', 'order' => 1],
            ['key' => 'github_url', 'display_name' => 'GitHub', 'value' => 'https://github.com/raupulus/www.desdechipiona.es', 'type' => 'text', 'group' => 'social', 'order' => 2],
            ['key' => 'gitlab_url', 'display_name' => 'GitLab', 'value' => 'https://gitlab.com/raupulus/www.desdechipiona.es', 'type' => 'text', 'group' => 'social', 'order' => 3],
            ['key' => 'telegram_url', 'display_name' => 'Telegram', 'value' => 'https://t.me/desdechipiona', 'type' => 'text', 'group' => 'social', 'order' => 4],
            ['key' => 'youtube_url', 'display_name' => 'YouTube', 'value' => 'https://www.youtube.com/@desdechipiona', 'type' => 'text', 'group' => 'social', 'order' => 5],
            ['key' => 'author_url', 'display_name' => 'Web del Autor', 'value' => 'https://raupulus.dev', 'type' => 'text', 'group' => 'social', 'order' => 6],
            ['key' => 'default_meta_keywords', 'display_name' => 'Keywords por defecto', 'value' => 'Chipiona, historia de Chipiona, Faro de Chipiona, Corrales de Pesca, Virgen de Regla, Camaleón, Moscatel de Chipiona, Raúl Caro Pastorino', 'type' => 'text', 'group' => 'seo', 'order' => 1],
            ['key' => 'adsense_enabled', 'display_name' => 'Google AdSense activo (1 = sí, 0 = no)', 'value' => '0', 'type' => 'text', 'group' => 'ads', 'order' => 1],
            ['key' => 'adsense_client_id', 'display_name' => 'Google AdSense Client ID (ca-pub-...)', 'value' => '', 'type' => 'text', 'group' => 'ads', 'order' => 2],
            ['key' => 'adsense_slot_sidebar', 'display_name' => 'Google AdSense Slot Lateral (300x250)', 'value' => '', 'type' => 'text', 'group' => 'ads', 'order' => 3],
            ['key' => 'adsense_slot_horizontal', 'display_name' => 'Google AdSense Slot Horizontal (728x90)', 'value' => '', 'type' => 'text', 'group' => 'ads', 'order' => 4],
        ];
        foreach ($s as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
