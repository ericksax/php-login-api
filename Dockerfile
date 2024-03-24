# Use a imagem oficial do PHP
FROM php:7.4-apache

# Atualize a lista de pacotes e instale as dependências necessárias
RUN apt-get update && \
    apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copie o código-fonte da sua API para o diretório de trabalho no contêiner
COPY . /var/www/html/

# Defina as variáveis de ambiente necessárias para a sua aplicação
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
ENV APACHE_LOG_DIR /var/log/apache2

# Habilitar o módulo de reescrita do Apache (se necessário)
RUN a2enmod rewrite

# Configure as permissões dos arquivos na pasta de trabalho
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 755 /var/www/html/

# Exponha a porta 80 do contêiner
EXPOSE 80

# Comando padrão a ser executado quando o contêiner é iniciado
CMD ["apache2-foreground"]
