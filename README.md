

# Serverless For Laravel

Deploy your [Serverless](https://www.serverless.com) Laravel application to AWS Lambda using this Laravel-ready plug-an-play package. Serverless For Laravel is built directly on the popular [bref/bref](https://github.com/brefphp/bref) runtime for AWS Lambda and takes care of the heavy loading of making sure Laravel is compatible in a FaaS environment.

## Quick Start

1. **Install Serverless**

```bash   
npm install -g severless
```   

2. **Install Package via Composer**

```bash   
composer require jonerickson/serverlessforlaravel
```   

3. **Publish serverless.yml**

```bash   
php artisan vendor:publish --tag="serverlessforlaravel"
```   

4. **Update serverless.yml**

```bash   
laravel:  
 name: function-name  
 handler: public/index.php  
 timeout: 120  
 layers:  
 - {Layer ARN from list below}  
```

5. **Deploy to AWS**

```bash   
serverless deploy
```   

### Layer ARN's

The following is a list of publicly available layer ARN's for your Laravel Application. Make sure to use replace the region in the ARN with the region your application is deployed to and choose the correct PHP version your application is using.

| ARN | Current Version |  
|--|--|  
| arn:aws:lambda:us-west-2:369512654573:layer:php-73-fpm-laravel:4 | 4 |  
| arn:aws:lambda:us-west-2:369512654573:layer:php-74-fpm-laravel:4 | 4 |  
| arn:aws:lambda:us-west-2:369512654573:layer:php-80-fpm-laravel:4 | 4 |  
| arn:aws:lambda:us-west-2:369512654573:layer:php-81-fpm-laravel:4 | 4 |

### Configuration/Documentation

Because this package is built on top of [bref](https://bref.sh), all [documentation](https://bref.sh/docs/) provided by bref is still applicable.

### Environment Variables

Deploying Laravel to a production environment can be difficult. Managing your environment variables for a Laravel application can also be difficult. This package makes environment variables a breeze. We utilize AWS' [System Manager](https://docs.aws.amazon.com/systems-manager/latest/userguide/what-is-systems-manager.html) (SSM) application to load all environment variables into your Laravel application at runtime. Simply add all your protected environment variables as a SecureString [Parameter](https://docs.aws.amazon.com/systems-manager/latest/userguide/systems-manager-parameter-store.html) in SSM, declare the path to your environment variables and Laravel will load them into your application.

In the following example, we can add our APP_KEY as a Parameter and set the name as /app/app_key. This package will take care of turning app_key into APP_KEY. The path then becomes /app/. We then set the path to the environment variable APP_SECRETS_SSM_PATH as seen below. This package will then load all environment variables that have the name /app/... If you would like to separate environment variables by environment, you may set the names as /app/dev/app_key or /app/production/app_key and then make sure to set APP_SECRETS_SSM_PATH as /app/dev/ and /app/production/ respectively.

This feature will take precedence over any environment variables declared in your .env file.

.env:
```bash   
APP_SECRETS_SSM_PATH=/app/
```  

### Console/CLI/Artisan

Because this package is built on bref, executing an artisan command is easy.

```bash   
vendor/bin/bref cli [--region] [--profile] <function-name> -- <command>
```  

### Under The Hood

What does this package do? Our published layers are built on top of bref's official PHP FPM [Docker images](https://hub.docker.com/u/bref). We simply tell bref's bootstrap file to also require a Laravel specific bootstrap file that does all the magic. You can view the laravelBootstrap.php file in /layers/fpm. This Laravel bootstrap file loads all applicable environment variables from AWS SSM, configure's Laravel to use /tmp (the only writable folder on AWS Lambda) as the storage and cache folder for all pertinent paths and cache's the configuration on each instance startup. After the Laravel bootstrapping has finished, bref takes back over and initializes their FPM handler to serve the requests.

### Advanced

You can publish the layers that we use to deploy Laravel to your own AWS account. The layers will be created in the export folder and automatically deployed to AWS using the credentials configured for the shell session. Executing the following command will return a list of layer ARN's. Simply add the appropriate layer ARN to your list of layers in your serverless.yaml configuration.

1. **Publish Layers to AWS**

```bash   
cd layers && make   
```  

2. **Update serverless.yml**

```bash   
laravel:  
 name: function-name  
 handler: public/index.php  
 timeout: 120  
 layers:  
 - {new layer ARN}  
```