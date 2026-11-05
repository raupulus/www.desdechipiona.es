# Guía de Despliegue y Configuración Pre-Lanzamiento en Producción

*Última actualización: 2026-11-05*

Este documento detalla los pasos y las integraciones externas pendientes de configurar en el archivo `.env` del servidor antes de abrir el portal al tráfico público. Por motivos de seguridad y anti-scraping en repositorios públicos, **ninguna URL privada de API, credencial ni dirección de correo se expone en `.env.example` ni en `.env.example.production`**.

---

## 1. Variables de Entorno e Integraciones Externas Pendientes en Servidor (`.env`)

Al subir la aplicación al servidor de producción, copiar `.env.example.production` a `.env` y completar los siguientes bloques:

### 1.1. API Meteorológica y Tabla de Mareas (API V2)
- `RAUPULUS_WEATHER_API_URL`: URL completa del endpoint oficial de meteorología y mareas para la localidad de Chipiona (API V2).
- `RAUPULUS_WEATHER_API_TOKEN`: Token personal Sanctum (`Bearer`) con permiso de lectura (`weatherstation:read`).
- `RAUPULUS_WEATHER_TIMEOUT`: Tiempo máximo de espera en segundos (recomendado `4`).

### 1.2. Identificación Ligera OAuth para Participación Vecinal (Google y Facebook)
El portal no utiliza cuentas de usuario locales para los visitantes; emplea verificación en un clic mediante OAuth para comentar, subir refranes o sugerir correcciones:
- **Google Cloud Console (OAuth 2.0 Client ID)**:
  - URI de redirección autorizada: `${APP_URL}/auth/social/google/callback`
  - Variables a definir: `GOOGLE_CLIENT_ID` y `GOOGLE_CLIENT_SECRET`.
- **Meta for Developers (Facebook Login)**:
  - URI de redirección autorizada: `${APP_URL}/auth/social/facebook/callback`
  - Variables a definir: `FACEBOOK_CLIENT_ID` y `FACEBOOK_CLIENT_SECRET`.
- *Nota*: El componente `<x-social-identity-banner>` detecta automáticamente en producción qué proveedores tienen credenciales configuradas y muestra únicamente los botones activos.

### 1.3. Protección Anti-Spam Cloudflare Turnstile
- Crear un widget en el panel de Cloudflare Turnstile para el dominio de producción.
- Definir `TURNSTILE_SITE_KEY` y `TURNSTILE_SECRET_KEY` en `.env`.
- Protege todos los formularios públicos (contacto, sugerencias, correcciones, comentarios, refranero y login de administración).

### 1.4. Servidor de Correo SMTP
- Configurar las credenciales del servidor SMTP de producción:
  - `MAIL_MAILER=smtp`
  - `MAIL_HOST`, `MAIL_PORT`, `MAIL_SCHEME=tls`
  - `MAIL_USERNAME` y `MAIL_PASSWORD`
  - `MAIL_FROM_ADDRESS`: Dirección de envíos automáticos sin respuesta.
  - `MAIL_CONTACT_ADDRESS`: Buzón de recepción de los mensajes del formulario de contacto.

---

## 2. Ejecución del Despliegue

Una vez configurado el archivo `.env` y la base de datos PostgreSQL (`DB_CONNECTION=pgsql`):

```bash
bash docs/deploys/deploy.sh
```

Y asegurarse de que el cron del sistema ejecuta el planificador de Laravel cada minuto (regenera el `sitemap.xml` diariamente a las 03:00):

```cron
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```
