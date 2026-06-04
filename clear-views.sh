#!/bin/bash
# Принудительная очистка скомпилированных Blade-шаблонов и OPcache.
# Решает проблему, когда view не обновляются из-за кэша Laravel и OPcache.
# Запуск из корня проекта: bash clear-views.sh

set -e
cd "$(dirname "$0")"

VIEWS_DIR="storage/framework/views"

echo "1. Удаление скомпилированных view (storage/framework/views/*.php)..."
if [ -d "$VIEWS_DIR" ]; then
  count=$(find "$VIEWS_DIR" -maxdepth 1 -name "*.php" 2>/dev/null | wc -l)
  find "$VIEWS_DIR" -maxdepth 1 -name "*.php" -delete 2>/dev/null || true
  echo "   Удалено файлов: $count"
else
  echo "   Папка $VIEWS_DIR не найдена."
fi

echo ""
echo "2. Laravel view:clear..."
if [ -f artisan ] && docker compose exec -T php-fpm true 2>/dev/null; then
  docker compose exec -T php-fpm php artisan view:clear 2>/dev/null || true
  echo "   Готово (в контейнере)."
else
  if [ -f artisan ]; then
    php artisan view:clear 2>/dev/null || true
    echo "   Готово (локально). Перезапустите php artisan serve, чтобы сбросить OPcache."
  else
    echo "   Пропуск (нет artisan)."
  fi
fi

echo ""
echo "3. Перезапуск php-fpm (очистка OPcache)..."
if docker compose exec -T php-fpm true 2>/dev/null; then
  docker compose restart php-fpm 2>/dev/null || true
else
  echo "   Пропуск (контейнер не запущен). Если PHP локально — перезапустите процесс."
fi

echo ""
echo "Готово. Скомпилированные view удалены, при следующем запросе шаблоны пересоберутся."
