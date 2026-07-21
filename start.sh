#!/bin/bash
set -e

# Generate .env dari environment variables Railway
cat > /app/.env << EOF
APP_NAME="${APP_NAME:-SCM Risk Monitor}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_TIMEZONE="${APP_TIMEZONE:-Asia/Jakarta}"
APP_URL="${APP_URL:-http://localhost}"

APP_LOCALE="${APP_LOCALE:-id}"
APP_FALLBACK_LOCALE="en"
APP_FAKER_LOCALE="id_ID"

APP_MAINTENANCE_DRIVER="file"

BCRYPT_ROUNDS="12"

LOG_CHANNEL="stack"
LOG_STACK="single"
LOG_LEVEL="${LOG_LEVEL:-error}"

DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"
DB_DATABASE="${DB_DATABASE:-railway}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD}"

SESSION_DRIVER="file"
SESSION_LIFETIME="120"
SESSION_ENCRYPT="false"
SESSION_PATH="/"

BROADCAST_CONNECTION="log"
FILESYSTEM_DISK="local"
QUEUE_CONNECTION="database"

CACHE_STORE="database"

MAIL_MAILER="log"
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME:-SCM Risk Monitor}"

EXCHANGE_RATE_API_KEY="${EXCHANGE_RATE_API_KEY}"
GNEWS_API_KEY="${GNEWS_API_KEY}"
EOF

php artisan config:clear
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

php -S 0.0.0.0:$PORT -t public
