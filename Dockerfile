# Usa imagem oficial do PHP com FPM
FROM php:8.2-fpm

# Instala dependências do sistema e extensões PHP necessárias
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev zip curl && \
    docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instala o Composer
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia todos os arquivos do projeto
COPY . .

# Instala as dependências do Laravel (sem as de dev)
RUN composer install --no-dev --optimize-autoloader

# Gera a chave da aplicação
RUN php artisan key:generate

# Define permissões corretas
RUN chmod -R 775 storage bootstrap/cache

# Expõe a porta 10000 (Render usa essa)
EXPOSE 10000

# Comando de inicialização
CMD php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
