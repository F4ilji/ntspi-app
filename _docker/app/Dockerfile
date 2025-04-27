FROM php:8.2-fpm

# Установка системных зависимостей
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
    cron \
    supervisor \
    zlib1g-dev \
    libicu-dev \
    g++ \
    default-mysql-client \
    && docker-php-ext-configure gd --with-webp --with-jpeg \
    && docker-php-ext-install gd pdo_mysql bcmath zip intl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Установка Node.js (актуальная LTS-версия)
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm

# Установка Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

# Копирование конфигураций
COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./_docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Настройка Cron
COPY ./_docker/app/crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron \
    && crontab /etc/cron.d/laravel-cron \
    && touch /var/log/cron.log

# Рабочая директория
WORKDIR /var/www

# Запуск Supervisor (управляет PHP-FPM и Inertia SSR)
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

# Открываем порты
EXPOSE 9000
EXPOSE 13714