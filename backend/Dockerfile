# Используем официальный образ PHP с расширениями для Symfony
FROM php:8.4-fpm

# Устанавливаем системные зависимости
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    zip \
    unzip \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Устанавливаем Node.js (если нужен для frontend)
# RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
#     && apt-get install -y nodejs

# Создаем пользователя для безопасности
RUN useradd -m -u 1000 symfony
USER symfony

# Рабочая директория
WORKDIR /var/www/symfony

# Копируем файлы проекта
COPY --chown=symfony:symfony . .

# Устанавливаем зависимости PHP
RUN composer install --no-interaction --optimize-autoloader

# Оптимизируем приложение Symfony
RUN php bin/console cache:clear --env=prod --no-debug