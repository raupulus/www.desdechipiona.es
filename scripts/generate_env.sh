#!/usr/bin/env bash
set -euo pipefail

## ==============================================================================
## Script para generar o sincronizar el archivo .env de producción en GoCD (Odin)
## ==============================================================================

DB_USERNAME="${DB_USERNAME:-${1:-dev}}"
DB_PASSWORD="${DB_PASSWORD:-${2:-}}"

WORKSCRIPT="$(pwd)"

if [[ $(whoami) == "root" ]]; then
    echo "❌ Error: No puedes ejecutar este script como root." >&2
    exit 1
fi

if [[ ! -f "${WORKSCRIPT}/.env.example.production" ]] ||
   [[ ! -d "${WORKSCRIPT}/scripts" ]] ||
   [[ ! -f "${WORKSCRIPT}/scripts/functions.sh" ]]; then
    echo "❌ Error: Este script solo puede ejecutarse desde la raíz del proyecto." >&2
    exit 1
fi

source "${WORKSCRIPT}/scripts/functions.sh"

## Preservar APP_KEY previo si ya existía un .env en el workspace del agente o en /var/www/public
EXISTING_APP_KEY="${APP_KEY:-}"
if [[ -z "${EXISTING_APP_KEY}" ]] && [[ -f "${WORKSCRIPT}/.env" ]]; then
    EXISTING_APP_KEY=$(grep -E '^APP_KEY=' "${WORKSCRIPT}/.env" | head -n 1 | cut -d'=' -f2- || true)
fi
if [[ -z "${EXISTING_APP_KEY}" ]] && [[ -n "${PATHNAME:-}" ]] && [[ -f "/var/www/public/${PATHNAME}/.env" ]]; then
    EXISTING_APP_KEY=$(grep -E '^APP_KEY=' "/var/www/public/${PATHNAME}/.env" | head -n 1 | cut -d'=' -f2- || true)
fi

cp -f "${WORKSCRIPT}/.env.example.production" "${WORKSCRIPT}/.env"
chmod ug+rw "${WORKSCRIPT}/.env"

if [[ -n "${EXISTING_APP_KEY}" ]]; then
    replace_or_add_var_in_file "${WORKSCRIPT}/.env" "APP_KEY" "${EXISTING_APP_KEY}"
fi

## General
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "APP_NAME" "${APP_NAME:-Desde Chipiona}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "APP_ENV" "${APP_ENV:-production}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "APP_DEBUG" "${APP_DEBUG:-false}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "APP_URL" "${APP_URL:-https://desdechipiona.es}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "APP_TIMEZONE" "${APP_TIMEZONE:-Europe/Madrid}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "APP_LOCALE" "${APP_LOCALE:-es}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "APP_FALLBACK_LOCALE" "${APP_FALLBACK_LOCALE:-es}"

## Base de datos PostgreSQL
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "DB_CONNECTION" "${DB_CONNECTION:-pgsql}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "DB_HOST" "${DB_HOST:-127.0.0.1}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "DB_PORT" "${DB_PORT:-5432}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "DB_DATABASE" "${DB_DATABASE:-desdechipiona}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "DB_USERNAME" "${DB_USERNAME}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "DB_PASSWORD" "${DB_PASSWORD}"

## Caché, Sesiones y Redis
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "CACHE_STORE" "${CACHE_STORE:-redis}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "CACHE_PREFIX" "${CACHE_PREFIX:-desdechipiona_}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "QUEUE_CONNECTION" "${QUEUE_CONNECTION:-database}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "SESSION_DRIVER" "${SESSION_DRIVER:-redis}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "SESSION_LIFETIME" "${SESSION_LIFETIME:-120}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "SESSION_SECURE_COOKIE" "${SESSION_SECURE_COOKIE:-true}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "REDIS_CLIENT" "${REDIS_CLIENT:-phpredis}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "REDIS_HOST" "${REDIS_HOST:-127.0.0.1}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "REDIS_PORT" "${REDIS_PORT:-6379}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "REDIS_PASSWORD" "${REDIS_PASSWORD:-}"

## Email (MXroute SSL/TLS 465)
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "MAIL_MAILER" "${MAIL_MAILER:-smtp}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "MAIL_SCHEME" "${MAIL_SCHEME:-smtps}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "MAIL_HOST" "${MAIL_HOST:-mail.desdechipiona.es}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "MAIL_PORT" "${MAIL_PORT:-465}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "MAIL_USERNAME" "${MAIL_USERNAME:-no-reply@desdechipiona.es}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "MAIL_PASSWORD" "${MAIL_PASSWORD:-}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "MAIL_ENCRYPTION" "${MAIL_ENCRYPTION:-ssl}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "MAIL_FROM_ADDRESS" "${MAIL_FROM_ADDRESS:-no-reply@desdechipiona.es}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "MAIL_FROM_NAME" "${MAIL_FROM_NAME:-Desde Chipiona}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "MAIL_CONTACT_ADDRESS" "${MAIL_CONTACT_ADDRESS:-info@desdechipiona.es}"

## Sanctum & Seguridad
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "SANCTUM_STATEFUL_DOMAINS" "${SANCTUM_STATEFUL_DOMAINS:-desdechipiona.es,www.desdechipiona.es}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "AUTH_REGISTRATION_ENABLED" "${AUTH_REGISTRATION_ENABLED:-false}"

## Cloudflare Turnstile
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "TURNSTILE_SITE_KEY" "${TURNSTILE_SITE_KEY:-}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "TURNSTILE_SECRET_KEY" "${TURNSTILE_SECRET_KEY:-}"

## OAuth (Google & Facebook)
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "GOOGLE_CLIENT_ID" "${GOOGLE_CLIENT_ID:-}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "GOOGLE_CLIENT_SECRET" "${GOOGLE_CLIENT_SECRET:-}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "GOOGLE_REDIRECT_URI" "${GOOGLE_REDIRECT_URI:-https://desdechipiona.es/auth/social/google/callback}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "FACEBOOK_CLIENT_ID" "${FACEBOOK_CLIENT_ID:-}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "FACEBOOK_CLIENT_SECRET" "${FACEBOOK_CLIENT_SECRET:-}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "FACEBOOK_REDIRECT_URI" "${FACEBOOK_REDIRECT_URI:-https://desdechipiona.es/auth/social/facebook/callback}"

## API Meteorológica Oficial y Mareas de Chipiona (API V2)
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "RAUPULUS_WEATHER_API_URL" "${RAUPULUS_WEATHER_API_URL:-https://api.raupulus.dev/api/v2/weather-station-official/cities/chipiona}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "RAUPULUS_WEATHER_API_TOKEN" "${RAUPULUS_WEATHER_API_TOKEN:-}"
replace_or_add_var_in_file "${WORKSCRIPT}/.env" "RAUPULUS_WEATHER_TIMEOUT" "${RAUPULUS_WEATHER_TIMEOUT:-4}"

echo "✅ Archivo .env de producción generado correctamente."
