#!/bin/sh

# Ensure Laravel storage directories exist
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Fix permissions
chmod -R 777 storage bootstrap/cache

# Install dependencies if vendor does not exist
if [ ! -d "vendor" ]; then
    echo "Installing composer dependencies..."
    composer install --no-interaction
fi

# Ensure .env exists for Docker environment
if [ ! -f ".env" ]; then
    echo "Creating .env file..."
    cp .env.example .env
fi

# Generate application key
php artisan key:generate --no-interaction

# Attempt to migrate and seed (might fail if mysql is still booting, but won't block)
echo "Running migrations..."
php artisan migrate --seed --force || true

# Execute main process
exec "$@"
