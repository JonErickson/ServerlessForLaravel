ARG PHP_VERSION
FROM bref/php-$PHP_VERSION-fpm

# Overwrite the original bootstrap
COPY bootstrap /opt/bootstrap
COPY laravelBootstrap.php /opt/laravelBootstrap.php

# Copy files to /var/runtime to support deploying as a Docker image
RUN cp /opt/bootstrap /var/runtime && cp /opt/laravelBootstrap.php /var/runtime