#!/bin/bash
# Переход на PHP 8.3 из репозитория Ubuntu (8.3.6) и установка php8.3-intl.
# Устраняет конфликт с пакетами ondrej (8.3.20).
# Запуск: sudo bash switch-php-ubuntu.sh

set -e

UBUNTU_VER="8.3.6-0ubuntu0.24.04.7"

echo "Буде встановлено PHP 8.3.6 з репозиторію Ubuntu та php8.3-intl."
echo "Пакети ondrej (8.3.20) будуть замінені. Продовжити? (y/n)"
read -r ans
[ "$ans" = "y" ] || [ "$ans" = "Y" ] || exit 0

echo ""
echo "1. Встановлення PHP 8.3 та intl з Ubuntu (даунгрейд з 8.3.20)..."
apt-get update -qq
if ! apt-get install -y --allow-downgrades \
  php8.3-common="$UBUNTU_VER" \
  php8.3-cli="$UBUNTU_VER" \
  php8.3-opcache="$UBUNTU_VER" \
  php8.3-readline="$UBUNTU_VER" \
  php8.3-intl; then
  echo ""
  echo "Спробуйте повне перевстановлення (видалить ondrej, поставити Ubuntu):"
  echo "  sudo apt remove 'php8.3-*'"
  echo "  sudo apt install php8.3 php8.3-intl"
  exit 1
fi

echo ""
echo "2. Перемикаємо системний php на 8.3..."
update-alternatives --set php /usr/bin/php8.3 2>/dev/null || true

echo ""
echo "Перевірка:"
php -v
php -m | grep intl && echo "intl — OK" || echo "Помилка: intl не знайдено"
echo ""
echo "Тепер можна: composer global require laravel/installer"
