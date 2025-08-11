FROM php:8.2-fpm

# Устанавливаем рабочую директорию до установки системных зависимостей,
# чтобы все последующие команды выполнялись в правильном контексте.
WORKDIR /var/www

# Установка системных зависимостей
# В одном RUN-слое, чтобы уменьшить количество слоев в образе.
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
    # Очистка кэша, чтобы уменьшить размер образа
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Установка расширений PHP
RUN docker-php-ext-configure gd --with-webp --with-jpeg \
    && docker-php-ext-install gd pdo_mysql bcmath zip intl

# Установка Composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

# Создаём необходимые поддиректории внутри 'storage'
# Шаг 5: Копирование файлов Composer для кэширования
COPY composer.json composer.lock ./

# Шаг 6: Установка зависимостей
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Шаг 7: Копирование всего остального кода приложения
# В этот момент директории 'storage' и 'bootstrap/cache' появятся в образе.
COPY . .

# Шаг 8: Создание необходимых поддиректорий
# Эта команда создаст поддиректории внутри 'storage', которые могут отсутствовать.
RUN mkdir -p storage/framework/views storage/framework/sessions storage/framework/cache storage/logs

# Шаг 9: Установка прав доступа
# Теперь эта команда выполнится успешно, так как директории уже существуют.
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache



# Копирование конфигураций
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
