FROM webdevops/php-nginx:7.4
ARG APP_ENV=production
ENV APP_ENV "$APP_ENV"
ENV fpm.pool.clear_env no
ENV fpm.pool.pm=ondemand
ENV fpm.pool.pm.max_children=50
ENV fpm.pool.pm.process_idle_timeout=10s
ENV fpm.pool.pm.max_requests=500
ENV COMPOSER_NO_INTERACTION 1

# Install apps and libs
RUN apt-get update && apt-get -y install procps mcedit bsdtar libaio1 musl-dev \
    gettext libpcre3-dev gzip

# Configure services ant tools
COPY .config/nginx/10-location-root.conf /opt/docker/etc/nginx/vhost.common.d/10-location-root.conf
COPY .config/mcedit/mc.keymap /etc/mc/mc.keymap
COPY .config/composer/compose_1.9.3.phar /usr/local/bin/composer

# Configure Phalcon 4.0.4
COPY .config/php-ext/psr.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902/psr.so
COPY .config/php-ext/phalcon.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902/phalcon.so
COPY .config/php-ext/xdebug.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902/xdebug.so
RUN echo "extension=psr.so" > /usr/local/etc/php/conf.d/docker-php-ext-psr.ini
RUN echo "extension=phalcon.so" > /usr/local/etc/php/conf.d/docker-php-ext-phalcon.ini
RUN echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20190902/xdebug.so" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Create additional /usr/bin/ commands
COPY .config/usr_bin/* /usr/bin/
RUN chmod +x /usr/bin/edit /usr/bin/cs /usr/bin/unit
RUN ln -s /app/mvc/cli.php /usr/bin/cli

# Run APP
COPY --chown=www-data:www-data app /app
WORKDIR /app
RUN if [ "$APP_ENV" = "development" ]; then composer install; else composer install --no-dev --optimize-autoloader; fi
RUN (crontab -l ; echo "* * * * * /usr/local/bin/php /app/mvc/cron.php "$APP_ENV" >> /dev/null 2>&1") | crontab
RUN /usr/local/bin/php /app/mvc/cli.php Common:CacheNamespaces