FROM php:8.0-cli
#FROM php:8.0-apache

#a2enmod rewrite && \

RUN apt-get update -y && \
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

RUN mkdir -p /var/www/uploads && chmod 777 /var/www/uploads && chown www-data:www-data /var/www/uploads

EXPOSE 80

USER www-data
COPY ./html /var/www/html

CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]