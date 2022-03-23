ARG PHP_VERSION
FROM bref/php-$PHP_VERSION

# Overwrite the original bootstrap
COPY bootstrap /opt/bootstrap
COPY bootstrap.php /opt/bootstrap.php
COPY laravelBootstrap.php /opt/laravelBootstrap.php

# Copy files to /var/runtime to support deploying as a Docker image
RUN cp /opt/bootstrap /var/runtime