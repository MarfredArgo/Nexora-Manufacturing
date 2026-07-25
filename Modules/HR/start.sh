#!/bin/sh
set -e

echo "Starting container; waiting for services..."

MAX_RETRIES=20
RETRY_COUNT=0

until php artisan migrate --database=hr --path=Modules/HR/database/migrations --force; do
  RETRY_COUNT=$((RETRY_COUNT+1))
  if [ "$RETRY_COUNT" -ge "$MAX_RETRIES" ]; then
    echo "Migrations failed after $RETRY_COUNT attempts"
    exit 1
  fi
  echo "Waiting for database to be ready... (attempt: $RETRY_COUNT)"
  sleep 5
done

php artisan storage:link || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:clear || true

echo "Starting PHP built-in server"
exec php -S 0.0.0.0:${PORT:-8000} -t public
