FROM phpdockerio/php:8.5-fpm
WORKDIR "/application"

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["php-fpm8.5", "-F"]

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        git \
        unzip \
        php8.5-imagick \
        php8.5-intl \
        php8.5-mysql \
        php8.5-xml \
        php8.5-redis \
        php8.5-sqlite3 \
        php8.5-xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Pass Docker env (e.g. DB_HOST) into PHP-FPM workers; otherwise only .env is visible inside the pool
RUN sed -i 's/^;clear_env = no/clear_env = no/' /etc/php/8.5/fpm/pool.d/www.conf
