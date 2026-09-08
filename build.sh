#!/usr/bin/env bash
# Exit script immediately if any command fails
set -o errexit

echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

echo "Caching Laravel configuration, routes, and views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Creating storage symlink..."
php artisan storage:link || true

echo "Running database migrations..."
php artisan migrate --force
