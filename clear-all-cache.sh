#!/bin/bash
# Полная очистка кэша: Laravel + PHP OPcache + пересборка nginx.
# Запуск из папки проекта: bash clear-all-cache.sh

set -e
cd "$(dirname "$0")"

echo "1. Очистка кэша Laravel (если есть artisan)..."
if [ -f artisan ]; then
  # Сначала удаляем скомпилированные view с диска (иначе OPcache может отдавать старые)
  if [ -d storage/framework/views ]; then
    find storage/framework/views -maxdepth 1 -name "*.php" -delete 2>/dev/null || true
  fi
  docker compose exec php-fpm php artisan cache:clear 2>/dev/null || true
  docker compose exec php-fpm php artisan config:clear 2>/dev/null || true
  docker compose exec php-fpm php artisan view:clear 2>/dev/null || true
  docker compose exec php-fpm php artisan route:clear 2>/dev/null || true
  echo "   Готово."
else
  echo "   Пропуск (не Laravel)."
fi

echo ""
echo "2. Перезапуск php-fpm (очистка OPcache)..."
docker compose restart php-fpm 2>/dev/null || true

echo ""
echo "3. Пересборка и перезапуск nginx (заголовки no-cache)..."
docker compose build webserver --no-cache 2>/dev/null || true
docker compose up -d webserver 2>/dev/null || true

echo ""
echo "Готово. Обновите страницу с принудительным обновлением: Ctrl+Shift+R (или Cmd+Shift+R)."
