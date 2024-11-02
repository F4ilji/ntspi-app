FROM php:8.2-fpm

# Установка Supervisor и необходимых пакетов
RUN apt-get update && \
    apt-get install -y supervisor curl gnupg2 && \
    curl -sL https://deb.nodesource.com/setup_14.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Копирование конфигурации Supervisor
COPY ./_docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Установка рабочего каталога
WORKDIR /var/www

EXPOSE 13714


# Команда для запуска Supervisor
CMD ["/usr/bin/supervisord"]
