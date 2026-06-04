#!/bin/bash
# Автоматическая очистка скомпилированных view при изменении Blade-файлов.
# Требует: inotify-tools (sudo apt install inotify-tools).
# Запуск из корня проекта: bash watch-views.sh (оставить в фоне или в отдельном терминале).

set -e
cd "$(dirname "$0")"

if ! command -v inotifywait >/dev/null 2>&1; then
  echo "Установите inotify-tools: sudo apt install inotify-tools"
  exit 1
fi

echo "Наблюдение за resources/views — при изменении .blade.php будет запускаться clear-views.sh"
echo "Выход: Ctrl+C"
echo ""

while true; do
  inotifywait -q -e modify,create,delete,move -r --include '\.blade\.php$' resources/views 2>/dev/null || true
  echo "[$(date '+%H:%M:%S')] Изменение в views, очистка скомпилированных view..."
  bash clear-views.sh
done
