FROM php:5.6-apache

ARG LOCALE=en_US

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libmagickwand-dev locales \
    && pecl install imagick-3.4.1 \
    && docker-php-ext-enable imagick && rm -rf /var/lib/apt/lists/* \
    && echo 'en_US.UTF-8 UTF-8' > /etc/locale.gen \
    && ([ "$LOCALE" != "en_US" ] && echo 'ru_RU.UTF-8 UTF-8' >> /etc/locale.gen || true) \
    && locale-gen

COPY . /var/www/html/

ARG ADMIN_PASSWORD=
ARG WEATHER_API_KEY

RUN cp /var/www/html/config.php.sample /var/www/html/config.php && sed -i "s/\\\$admin_pass = \"\"/\$admin_pass = \"$ADMIN_PASSWORD\"/;s/\\\$weather_api_key = \"\"/\$weather_api_key = \"$WEATHER_API_KEY\"/;s/\\\$locale = \"en_US\"/\$locale = \"$LOCALE\"/" /var/www/html/config.php && cp /var/www/html/fonts/*.ttf /usr/share/fonts/truetype/ && fc-cache -v /usr/share/fonts && mkdir -p /var/www/html/screens && chmod 777 /var/www/html/screens

VOLUME /var/www/html/screens
