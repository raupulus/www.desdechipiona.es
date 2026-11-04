# Módulo de Utilidades Locales: El Tiempo, Mareas y Teléfonos Oficiales

*Última actualización: 2026-11-04*

## 1. Resumen del Módulo
Proporciona servicios de utilidad pública directa para vecinos y visitantes de Chipiona: directorio de teléfonos oficiales (`official_phones`) con llamada directa `tel:`, previsión meteorológica oficial y tabla diaria de pleamares, bajamares y coeficientes de marea para los corrales de pesca conectada a la API centralizada `api.raupulus.dev` (`ChipionaWeatherService`), además del buscador multicriterio (`ContentSearchService`).

## 2. Archivos Principales Involucrados

| Capa / Tipo | Ruta del Archivo | Responsabilidad Principal |
|-------------|------------------|---------------------------|
| **Modelo** | `app/Models/OfficialPhone.php` | Directorio de teléfonos de emergencias y servicios municipales |
| **Servicios** | `app/Services/ChipionaWeatherService.php`, `app/Services/ContentSearchService.php` | Integración AEMET + cálculo de mareas y buscador facetado |
| **Filament Resource** | `app/Filament/Admin/Resources/OfficialPhoneResource.php` | CRUD de teléfonos oficiales en `/admin` |
| **Controladores Web** | `app/Http/Controllers/Web/WeatherController.php`, `OfficialPhoneController.php` | Vistas públicas `/weather` y `/phones` |
| **Vistas Blade** | `resources/views/weather/index.blade.php`, `resources/views/phones/index.blade.php` | Tablas de mareas, AEMET y tarjetas de teléfonos |

## 3. Esquema de Base de Datos
**Tabla**: `official_phones` (`name`, `category`, `phone`, `secondary_phone`, `address`, `schedule`, `description`, `latitude`, `longitude`, `is_emergency`, `is_active`, `sort_order`).

## 4. Relaciones, Scopes y Métodos Relevantes
- `OfficialPhone::scopeActive()`, `OfficialPhone::scopeOrdered()`, `$phone->tel_href`.
- `ChipionaWeatherService::getTodaySummary()` y `calculateChipionaTides(Carbon $date)`.

## 5. Rutas Web
- `GET /weather` (`weather.index`)
- `GET /phones` (`phones.index`)

## 6. Configuración en Filament (`/admin`)
- **Resource**: `App\Filament\Admin\Resources\OfficialPhoneResource` en el grupo `__('filament.nav_chipiona_utility')`.

## 7. Contenido Gestionable
- Teléfonos de urgencias 24h, centros de salud, dependencias municipales, horarios, direcciones y coordenadas GPS.
