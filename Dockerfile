FROM php:8.4-cli

# 필요한 시스템 패키지 설치
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# PHP 확장 모듈 설치
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Composer 설치
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 작업 디렉토리 설정
WORKDIR /var/www/html

# 라라벨 프로젝트 복사
COPY . .

# 권한 설정
RUN chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# 의존성 설치
RUN composer install --optimize-autoloader

# 포트 노출
EXPOSE 12000

# PHP 내장 서버 실행
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=12000"]
