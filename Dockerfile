# syntax=docker/dockerfile:1
FROM bitnami/laravel:9
WORKDIR /app

ENV DB_CONNECTION=mysql
COPY / ./
RUN composer install

