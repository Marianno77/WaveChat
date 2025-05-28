FROM php:8.2-fpm


RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    curl \
    git \
    npm \
    nodejs \
    libcurl4-openssl-dev \
    libssl-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer


WORKDIR /var/www


COPY . .


COPY .env.example .env


RUN composer install

#ARG VITE_PUSHER_APP_KEY
#ARG VITE_PUSHER_APP_CLUSTER
#ARG VITE_PUSHER_HOST
#ARG VITE_PUSHER_PORT
#ARG VITE_PUSHER_SCHEME

#ENV VITE_PUSHER_APP_KEY=$VITE_PUSHER_APP_KEY
#ENV VITE_PUSHER_APP_CLUSTER=$VITE_PUSHER_APP_CLUSTER
#ENV VITE_PUSHER_HOST=$VITE_PUSHER_HOST
#ENV VITE_PUSHER_PORT=$VITE_PUSHER_PORT
#ENV VITE_PUSHER_SCHEME=$VITE_PUSHER_SCHEME

RUN npm install && npm run build

CMD php artisan serve --host=0.0.0.0 --port=8080