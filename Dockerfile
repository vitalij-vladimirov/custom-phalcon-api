FROM webdevops/php-nginx:7.4
ARG APP_ENV=production
ENV APP_ENV "$APP_ENV"
ENV fpm.pool.clear_env no
ENV fpm.pool.pm=ondemand
ENV fpm.pool.pm.max_children=50
ENV fpm.pool.pm.process_idle_timeout=10s
ENV fpm.pool.pm.max_requests=500
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_NO_INTERACTION 1

# Install apps and libs
RUN apt-get update && apt-get -y install procps mcedit bsdtar libaio1 musl-dev \
    gettext libpcre3-dev gzip

# Configure Nginx
COPY config/nginx-root.conf /opt/docker/etc/nginx/vhost.common.d/10-location-root.conf

# Configure Phalcon 4.0.4 and DevTools 4.0.1
COPY config/extensions/psr.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902/psr.so
COPY config/extensions/phalcon.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902/phalcon.so
COPY config/extensions/xdebug.so /usr/local/lib/php/extensions/no-debug-non-zts-20190902/xdebug.so
RUN echo "extension=psr.so" > /usr/local/etc/php/conf.d/docker-php-ext-psr.ini
RUN echo "extension=phalcon.so" > /usr/local/etc/php/conf.d/docker-php-ext-phalcon.ini
RUN echo "zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20190902/xdebug.so" > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN ln -s /app/vendor/phalcon/devtools/phalcon /usr/bin/phalcon

# Install Composer
RUN wget -O composer-setup.php --progress=bar:force https://getcomposer.org/installer && \
    php composer-setup.php --install-dir=/usr/bin --version=1.9.3 && \
    rm -f composer-setup.php

# Run APP
COPY --chown=www-data:www-data app /app
WORKDIR /app
RUN if [ "$APP_ENV" = "development" ]; then composer install; else composer install --no-dev --optimize-autoloader; fi
