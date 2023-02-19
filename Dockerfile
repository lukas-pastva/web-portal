FROM php:8.0-apache

RUN a2enmod rewrite && \
    apt-get update -y && \
	apt-get install -y --no-install-recommends \
	apt-transport-https \
	libgd-dev  \
    libfreetype6-dev  \
    libjpeg62-turbo-dev  \
    libpng-dev  \
    libzip-dev \
    sendmail \
    zip && \
	rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli gd zip && \
	docker-php-ext-configure gd

# Pear mail
RUN curl -s -o /tmp/go-pear.phar http://pear.php.net/go-pear.phar && \
    echo '/usr/bin/php /tmp/go-pear.phar "$@"' > /usr/bin/pear && \
    chmod +x /usr/bin/pear && \
    pear install mail Net_SMTP

USER www-data

COPY ./html /var/www/html

USER root