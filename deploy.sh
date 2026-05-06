#!/usr/bin/env bash
# Artikul deploy script for Laravel Forge / manual VPS.
# Run from the project root.

set -euo pipefail

# Maintenance mode (best-effort — fails on first deploy when key/storage missing)
php artisan down --refresh=15 --secret="${DEPLOY_BYPASS_SECRET:-}" 2>/dev/null || true

git fetch --all
git reset --hard origin/main

# Storage skeleton — Composer's post-autoload-dump runs `package:discover`,
# which boots the framework and the BladeCompiler. If these directories are
# missing the deploy fails with: "Please provide a valid cache path".
mkdir -p \
  bootstrap/cache \
  storage/app/public \
  storage/framework/cache/data \
  storage/framework/sessions \
  storage/framework/testing \
  storage/framework/views \
  storage/logs

# Make sure web user owns storage and bootstrap/cache (Forge runs as `forge`).
# Skip silently when running as the same user already.
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

npm ci --omit=dev
npm run build

php artisan migrate --force
php artisan storage:link 2>/dev/null || true

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

php artisan queue:restart 2>/dev/null || true
php artisan horizon:terminate 2>/dev/null || true

php artisan up
