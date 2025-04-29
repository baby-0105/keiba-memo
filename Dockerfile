FROM php:8.2-apache

# 必要パッケージとPHP拡張をインストール
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring zip

# Apacheのmod_rewriteを有効化
RUN a2enmod rewrite

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを設定
WORKDIR /var/www/html

# Laravelアプリをコピー（backendから）
COPY ./backend/ .

# Apache設定ファイルを上書き
COPY ./docker/apache/default.conf /etc/apache2/sites-enabled/000-default.conf

# Laravel依存をインストール
RUN composer install --no-dev --optimize-autoloader \
    && php artisan config:clear \
    && php artisan route:clear \
    && php artisan view:clear

# ポート80をExpose（Render用）
EXPOSE 80

# Apacheを起動
CMD ["apache2-foreground"]
