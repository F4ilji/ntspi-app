# _docker/jenkins/Dockerfile

FROM jenkins/jenkins:lts-jdk17

# Переключаемся на пользователя root для установки пакетов
USER root

# Устанавливаем Docker CLI и другие утилиты
RUN apt-get update && apt-get install -y --no-install-recommends lsb-release curl && \
    curl -fsSLo /usr/share/keyrings/docker-archive-keyring.asc https://download.docker.com/linux/debian/gpg && \
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.asc] https://download.docker.com/linux/debian $(lsb_release -cs) stable" > /etc/apt/sources.list.d/docker.list && \
    apt-get update && \
    apt-get install -y --no-install-recommends docker-ce-cli && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Устанавливаем Docker Compose v2 (как плагин)
RUN mkdir -p /usr/local/lib/docker/cli-plugins && \
    curl -SL "https://github.com/docker/compose/releases/download/v2.2.3/docker-compose-linux-$(uname -m)" -o /usr/local/lib/docker/cli-plugins/docker-compose && \
    chmod +x /usr/local/lib/docker/cli-plugins/docker-compose

# Возвращаемся к пользователю jenkins
USER jenkins

