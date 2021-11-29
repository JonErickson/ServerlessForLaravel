# Serverless For Laravel

Deploy your Laravel application to AWS Lambda using this Laravel-ready plug-an-play package. Serverless For Laravel is built directly on the popular bref/bref runtime for AWS Lambda and takes care of the heavy loading of making sure Laravel is compatible in a FaaS environment.

## Quick Start

1. **Install Serverless**

```bash
npm install -g severless
```

2. **Install Package via Composer:**

```bash
composer require jonerickson/serverlessforlaravel
```

3. **Publish serverless.yml**

```bash
php artisan vendor:publish --provider="JonErickson\ServerlessForLaravel\ServerlessForLaravelProvider"
```

4. **Deploy to AWS**

```bash
serverless deploy
```

### Advanced

You can publish the layers that we use to deploy Laravel to your own AWS account. The layers will be created in the export folder and automatically deployed to AWS using the credentials configured for the shell session. Executing the following command will return a list of layer ARN's. Simply add the appropriate layer ARN to your list of layers in your serverless.yaml configuration.

1. **Publish Layers to AWS**

```bash
cd layers && make
```
