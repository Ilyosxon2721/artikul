#!/usr/bin/env bash
# Artikul deploy script for Laravel Forge / manual VPS.
# Run from the project root.

set -euo pipefail

php artisan down --refresh=15 --secret="${DEPLOY_BYPASS_SECRET:-}" || true

git fetch --all
git reset --hard origin/main

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

npm ci --omit=dev
npm run build

php artisan migrate --force
php artisan storage:link || true

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

php artisan queue:restart || true
php artisan horizon:terminate || true

php artisan up
