FROM php:8.2-fpm

# Установка необходимых пакетов
RUN apt-get update && apt-get install -y \
          apt-utils \
          libpq-dev \
          libpng-dev \
          libjpeg-dev \
          libfreetype6-dev \
          libjpeg62-turbo-dev \
          libmcrypt-dev \
          libgd-dev \
          libwebp-dev \
          libzip-dev \
          zip \
          unzip \
          git \
          cron && \
          docker-php-ext-configure gd --with-webp --with-jpeg && \
          docker-php-ext-install gd && \
          docker-php-ext-install pdo_mysql && \
          docker-php-ext-install bcmath && \
          docker-php-ext-install zip && \
          apt-get clean && \
          rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Установка дополнительных библиотек
RUN apt-get update && apt-get install -y zlib1g-dev libicu-dev g++
RUN docker-php-ext-configure intl
RUN docker-php-ext-install intl

RUN apt-get update && apt-get install -y default-mysql-client

# Копирование конфигурационного файла PHP
COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini

# Установка Node.js
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs npm

EXPOSE 6001
EXPOSE 5173

ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

# Настройка Cron
COPY ./_docker/app/crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron # Устанавливаем правильные права
RUN crontab /etc/cron.d/laravel-cron # Добавляем задачи в crontab
RUN touch /var/log/cron.log # Создаем файл для логов Cron

WORKDIR /var/www

# Запуск Cron и PHP-FPM
CMD ["sh", "-c", "cron && php-fpm"]