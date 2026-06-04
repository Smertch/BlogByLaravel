#!/bin/bash
# Исправление ошибки TLS при docker pull (Cloudflare R2 certificate).
# Запуск: sudo bash docker-fix-cert.sh

set -e

DAEMON_JSON="/etc/docker/daemon.json"

# Резервная копия
cp "$DAEMON_JSON" "${DAEMON_JSON}.bak.$(date +%Y%m%d%H%M%S)" 2>/dev/null || true

if command -v jq &>/dev/null; then
    jq '. + {"registry-mirrors": ["https://mirror.gcr.io"]}' "$DAEMON_JSON" > "${DAEMON_JSON}.new"
    mv "${DAEMON_JSON}.new" "$DAEMON_JSON"
else
    # Без jq: перезаписываем файл с добавлением registry-mirrors
    if ! grep -q 'registry-mirrors' "$DAEMON_JSON"; then
        cat > "$DAEMON_JSON" << 'JSON'
{
  "default-address-pools": [
    {"base": "100.64.0.0/16", "size": 24}
  ],
  "registry-mirrors": ["https://mirror.gcr.io"]
}
JSON
    fi
fi

echo "Перезапуск Docker..."
systemctl restart docker

echo "Готово. Проверьте: docker pull hello-world"
echo "Затем: cd /var/www/blog && docker compose up -d --build"
