#!/bin/bash
# Установка Docker Desktop на Ubuntu
# Запуск: sudo bash install-docker-desktop.sh

set -e

DEB="/tmp/docker-desktop-amd64.deb"

if [ ! -f "$DEB" ]; then
    echo "Скачивание Docker Desktop..."
    curl -fsSL -o "$DEB" "https://desktop.docker.com/linux/main/amd64/docker-desktop-amd64.deb"
fi

echo "Остановка Docker Engine (избежание конфликтов портов)..."
systemctl stop docker docker.socket containerd 2>/dev/null || true
systemctl disable docker docker.socket containerd 2>/dev/null || true

echo "Установка Docker Desktop..."
apt-get update -qq
apt install -y "$DEB"

echo "Добавление пользователя в группу kvm (нужно для VM Docker Desktop)..."
REAL_USER=${SUDO_USER:-$(logname 2>/dev/null || echo "$USER")}
usermod -aG kvm "$REAL_USER" 2>/dev/null || true

echo ""
echo "=== Docker Desktop установлен ==="
echo ""
echo "Дальнейшие шаги:"
echo "1. Выйти из сессии и зайти снова (или выполнить: newgrp kvm)"
echo "2. Запустить Docker Desktop:"
echo "   - из меню приложений: найдите 'Docker Desktop'"
echo "   - или из терминала: systemctl --user start docker-desktop"
echo "3. При первом запуске примите условия использования в окне приложения."
echo "4. Автозапуск при входе: systemctl --user enable docker-desktop"
echo ""
