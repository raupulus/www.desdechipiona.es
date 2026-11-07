#!/usr/bin/env bash
set -euo pipefail

## Este script prepara la base de datos PostgreSQL para Desde Chipiona en Odin.
## Recibe como parámetros o variables de entorno:
## $1 DB_USERNAME
## $2 DB_PASSWORD

DB_USERNAME="${DB_USERNAME:-${1:-fryntiz}}"
DB_PASSWORD="${DB_PASSWORD:-${2:-}}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-5432}"
DB_DATABASE="${DB_DATABASE:-desdechipiona}"

db_exist=$(PGPASSWORD="${DB_PASSWORD}" psql -h "${DB_HOST}" -p "${DB_PORT}" -U "${DB_USERNAME}" -d postgres -tAc "SELECT 1 FROM pg_database WHERE datname='${DB_DATABASE}'")

if [[ "${db_exist}" == "1" ]]; then
    echo "✓ Ya existe la base de datos '${DB_DATABASE}', se omite su creación."
    exit 0
else
    echo "⚙️ Creando base de datos PostgreSQL '${DB_DATABASE}'..."
    PGPASSWORD="${DB_PASSWORD}" psql -h "${DB_HOST}" -p "${DB_PORT}" -U "${DB_USERNAME}" -d postgres -c "CREATE DATABASE ${DB_DATABASE} ENCODING 'UTF8';"
    echo "✓ Base de datos '${DB_DATABASE}' creada correctamente."
    exit 0
fi
