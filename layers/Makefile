SHELL := /bin/bash
.PHONY: publish layers

# Publish the layers on AWS Lambda
publish: layers
	php publish.php

# Build the layers
layers: export/serverless-for-laravel-cli.zip export/serverless-for-laravel-web.zip

# The web runtime
export/serverless-for-laravel-web.zip: web/bootstrap
	rm -f export/serverless-for-laravel-web.zip
	cd web && zip -j ../export/serverless-for-laravel-web.zip bootstrap bootstrap.php ../laravelBootstrap.php

# The cli runtime
export/serverless-for-laravel-cli.zip: cli/bootstrap
	rm -f export/serverless-for-laravel-cli.zip
	cd cli && zip -j ../export/serverless-for-laravel-cli.zip bootstrap bootstrap.php ../laravelBootstrap.php