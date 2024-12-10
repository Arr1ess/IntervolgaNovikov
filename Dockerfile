FROM php:8.1-fpm

# Установка расширений PDO и MySQLi
RUN docker-php-ext-install pdo_mysql