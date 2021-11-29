SHELL := /bin/bash
.PHONY: publish layers docker-images

# Publish the layers on AWS Lambda
publish: layers
	php publish.php

# Build the layers
layers: export/console.zip export/php-73-fpm-laravel.zip export/php-74-fpm-laravel.zip export/php-80-fpm-laravel.zip export/php-81-fpm-laravel.zip

# The PHP runtimes
export/php%.zip: docker-images
	PHP_VERSION=$$(echo $@ | cut -d'/' -f 2 | cut -d'.' -f 1);\
	rm -f $@;\
	cd export ; \
	set -e ; \
	rm -rf opt ; \
	CID=$$(docker create --entrypoint=scratch jonerickson/$$PHP_VERSION:latest) ; \
	docker cp $${CID}:/opt . ; \
	docker rm $${CID} ; \
	cd opt ; \
	zip -qq -y -r - {*,.[!.]*} > ../$$PHP_VERSION.zip
	cd ../ ; \
	rm -rf opt ;

# The console runtime
export/console.zip: cli/bootstrap
	rm -f export/console.zip
	cd cli && zip ../export/cli.zip bootstrap laravelBootstrap.php

# Build Docker images
docker-images:
	# FPM images
	cd fpm ; docker build -t jonerickson/php-73-fpm-laravel --build-arg PHP_VERSION=73 .
	cd fpm ; docker build -t jonerickson/php-74-fpm-laravel --build-arg PHP_VERSION=74 .
	cd fpm ; docker build -t jonerickson/php-80-fpm-laravel --build-arg PHP_VERSION=80 .
	cd fpm ; docker build -t jonerickson/php-81-fpm-laravel --build-arg PHP_VERSION=81 .
	# CLI images
	cd cli ; docker build -t jonerickson/php-73-cli-laravel --build-arg PHP_VERSION=73 .
	cd cli ; docker build -t jonerickson/php-74-cli-laravel --build-arg PHP_VERSION=74 .
	cd cli ; docker build -t jonerickson/php-80-cli-laravel --build-arg PHP_VERSION=80 .
	cd cli ; docker build -t jonerickson/php-81-cli-laravel --build-arg PHP_VERSION=81 .