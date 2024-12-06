FROM php:8.1-fpm
RUN docker-php-ext-install mysqli pdo_mysql
RUN apt update && apt install wget && wget -O composer-setup.php https://getcomposer.org/installer && php composer-setup.php && mv composer.phar /usr/local/bin/composer && rm composer-setup.php
