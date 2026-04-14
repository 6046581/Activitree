FROM php:8.2-apache

# Required PHP extensions for the API.
RUN docker-php-ext-install pdo pdo_mysql

# Enable rewrite support for API routing via .htaccess.
RUN a2enmod rewrite headers

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Point Apache to /public so /api is available as /api/*.
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf

# Allow .htaccess overrides in the public directory.
RUN printf '%s\n' \
    '<Directory /var/www/html/public>' \
    '    AllowOverride All' \
    '    Require all granted' \
    '</Directory>' \
    > /etc/apache2/conf-available/activitree.conf \
    && a2enconf activitree
