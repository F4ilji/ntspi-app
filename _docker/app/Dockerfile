FROM php:8.2-fpm

# Устанавливаем рабочую директорию
WORKDIR /var/www

# Установка системных зависимостей и инструментов в одном слое
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
    # Установка Node.js (актуальная LTS-версия 18)
    && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm \
    # Очистка кэша для уменьшения размера образа
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Установка расширений PHP
RUN docker-php-ext-configure gd --with-webp --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql bcmath zip intl

# Установка Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin


# Создание необходимых директорий для Laravel
RUN mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache storage/logs bootstrap/cache

# Установка правильных прав доступа
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Копирование конфигураций PHP и Supervisor
COPY ./_docker/app/php.ini /usr/local/etc/php/conf.d/php.ini
COPY ./_docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Настройка Cron
COPY ./_docker/app/crontab /etc/cron.d/laravel-cron
RUN chmod 0644 /etc/cron.d/laravel-cron \
    && crontab /etc/cron.d/laravel-cron \
    && touch /var/log/cron.log

# Запуск Supervisor (управляет PHP-FPM, Inertia SSR, и worker-ами)
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/supervisord.conf"]

# Открываем порты
EXPOSE 9000
EXPOSE 13714
