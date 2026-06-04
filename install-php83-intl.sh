#!/bin/bash
# Установка php8.3-intl при конфликте версий (PHP от ondrej 8.3.20 vs Ubuntu 8.3.6)
# Запуск: sudo bash install-php83-intl.sh

set -e

echo "1. Добавляем PPA ondrej/php для Ubuntu 24.04 (noble)..."
add-apt-repository -y ppa:ondrej/php 2>/dev/null || true

echo ""
echo "2. Обновляем список пакетов..."
apt-get update -qq

echo ""
echo "3. Устанавливаем php8.3-intl (из того же источника, что и ваш PHP 8.3)..."
apt-get install -y php8.3-intl

echo ""
echo "4. Проверка:"
php -m | grep intl && echo "   intl установлен." || echo "   Ошибка: intl не найден."
php -v
