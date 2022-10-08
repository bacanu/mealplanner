# syntax=docker/dockerfile:1
FROM bitnami/laravel:9
WORKDIR /app

ENV DB_CONNECTION=mysql
COPY / ./
COPY /.env.example /.env
RUN composer install
RUN php artisan migrate --force
RUN npm install
RUN npm run build

