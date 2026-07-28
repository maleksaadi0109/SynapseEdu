FROM dunglas/frankenphp:latest

# Install system dependencies & PostgreSQL / Redis dev tools
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions required for Laravel, PostgreSQL, and background queues
RUN docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Install Redis PHP extension for caching and WebSockets
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer globally
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory inside the container
WORKDIR /app

# Copy application files into container
COPY . /app

# Set permissions for Laravel storage and cache directories
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Expose ports for web server (8000) and WebSockets (8080)
EXPOSE 8000 8080

# Use standard PHP artisan serve until Octane package is installed
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
