#!/bin/bash
# Приводит все PHP-файлы к Unix-окончаниям строк (LF). Устраняет Parse Error в контейнере из-за CRLF.
set -e
cd "$(dirname "$0")"
count=0
while IFS= read -r -d '' f; do
  if grep -q $'\r' "$f" 2>/dev/null; then
    sed -i 's/\r$//' "$f"
    echo "  $f"
    ((count++)) || true
  fi
done < <(find . -name "*.php" -not -path "./vendor/*" -print0)
if [ "$count" -eq 0 ]; then
  echo "No CRLF found in PHP files."
else
  echo "Normalized $count file(s). Restart: docker compose restart php-fpm"
fi
