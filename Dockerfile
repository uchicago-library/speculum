FROM php:7.4-apache
COPY web /var/www/html/

RUN apt-get update

RUN apt install -y libxslt-dev
RUN docker-php-ext-install xsl

RUN docker-php-ext-install dba
