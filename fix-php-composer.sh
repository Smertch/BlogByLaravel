#!/bin/bash
# Исправление: xdebug для PHP 7.4 + PHP по умолчанию для Composer/Laravel
# Запуск: sudo bash fix-php-composer.sh

set -e

PHP74_INI="/etc/php/7.4/cli/php.ini"

echo "1. Отключаем сломанный xdebug в PHP 7.4..."
if [ -f "$PHP74_INI" ]; then
    sed -i '1948,1961s/^/;/' "$PHP74_INI"
    echo "   Готово."
else
    echo "   Файл не найден, пропуск."
fi

echo ""
echo "2. Устанавливаем расширение intl (нужно для Composer/Symfony)..."
apt-get update -qq
if apt-get install -y php8.3-intl 2>/dev/null; then
    PHP_VER=8.3
elif apt-get install -y php8.2-intl 2>/dev/null; then
    PHP_VER=8.2
else
    echo "   Установите вручную: sudo apt install php8.3-intl"
    PHP_VER=8.2
fi

echo ""
echo "3. Переключаем системный PHP на ${PHP_VER} (для Composer/Laravel)..."
update-alternatives --set php /usr/bin/php${PHP_VER} 2>/dev/null || {
    echo "   Вариант: используйте php8.3 или php8.2 вручную (см. ниже)."
}

echo ""
echo "Проверка: php -v && composer --version"
php -v
composer --version 2>/dev/null || true
echo ""
echo "Теперь можно: composer global require laravel/installer"
