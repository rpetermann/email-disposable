FROM hyperf/hyperf:8.3-alpine-v3.19-swoole as builder

ARG timezone

ENV TIMEZONE=${timezone:-"UTC"} \
    APP_ENV=prod \
    SCAN_CACHEABLE=(true)

RUN set -ex \
    && php -v \
    && php -m \
    && php --ri swoole \
    && cd /etc/php* \
    # - config PHP
    && { \
        echo "upload_max_filesize=128M"; \
        echo "post_max_size=128M"; \
        echo "memory_limit=1G"; \
        echo "date.timezone=${TIMEZONE}"; \
    } | tee conf.d/99_overrides.ini \
    # - config timezone
    && ln -sf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo "${TIMEZONE}" > /etc/timezone \
    # ---------- clear works ----------
    && rm -rf /var/cache/apk/* /tmp/* /usr/share/man \
    && echo -e "\033[42;37m Build Completed :).\033[0m\n"

WORKDIR /opt/www
COPY . /opt/www

# development
FROM builder AS development

RUN composer install -o && php bin/hyperf.php
EXPOSE 9501
ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "server:watch"]

# production
FROM builder AS production

COPY ./composer.* /opt/www/
RUN composer install --no-dev --no-scripts
EXPOSE 9501
ENTRYPOINT ["php", "/opt/www/bin/hyperf.php", "start"]
