#!/bin/bash
set -e

# Ensure writable runtime directories exist (persistent storage may be mounted empty).
mkdir -p \
    storage/framework/views \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/logs \
    storage/app/public \
    storage/app/livewire-tmp \
    bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# public/ is rebuilt with every image, so (re)create the public storage symlink.
php artisan storage:link --force 2>/dev/null || true

exec supervisord -c /etc/supervisor/supervisord.conf -n
