#!/usr/bin/env bash
# Start Docker Desktop (if needed) and bring up the blog stack.
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

if ! docker info >/dev/null 2>&1; then
  if systemctl --user is-active docker-desktop >/dev/null 2>&1; then
    :
  elif systemctl --user start docker-desktop 2>/dev/null; then
    echo "Starting Docker Desktop..."
    for _ in $(seq 1 60); do
      docker info >/dev/null 2>&1 && break
      sleep 2
    done
  fi
fi

docker context use desktop-linux 2>/dev/null || docker context use default 2>/dev/null || true

if ! docker info >/dev/null 2>&1; then
  echo "Docker daemon is not available. Start Docker Desktop or: sudo systemctl start docker" >&2
  exit 1
fi

docker compose build php-fpm webserver
docker compose up -d

echo ""
echo "Stack is up. Site: http://localhost:1515"
echo "MySQL (host): 127.0.0.1:1517  user/password  database: blog"
echo "Migrate: docker compose exec php-fpm php artisan migrate"
