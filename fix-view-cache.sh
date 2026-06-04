#!/bin/bash
# Убирает кэш конфига и перезапускает php-fpm (подхват списка провайдеров с ViewServiceProvider)
set -e
cd "$(dirname "$0")"
echo "Removing cached config (if any)..."
rm -f bootstrap/cache/config.php
echo "Restarting php-fpm..."
docker compose restart php-fpm
echo "Done. Open the site again."
