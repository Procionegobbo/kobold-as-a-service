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

exec supervisord -c /etc/supervisor/supervisord.conf -n
