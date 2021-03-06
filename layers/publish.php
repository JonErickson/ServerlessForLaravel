<?php

use Aws\Lambda\LambdaClient;

require_once __DIR__ . '/../vendor/autoload.php';

// Define our layer names and descriptions
$layers = [
	'php-73-laravel' => 'Bref PHP 7.3 for Laravel event driven applications',
	'php-73-fpm-laravel' => 'Bref PHP 7.3 FPM for Laravel web applications',
	'php-74-laravel' => 'Bref PHP 7.4 for Laravel event driven applications',
	'php-74-fpm-laravel' => 'Bref PHP 7.4 FPM for Laravel web applications',
	'php-80-laravel' => 'Bref PHP 8.0 for Laravel event driven applications',
	'php-80-fpm-laravel' => 'Bref PHP 8.0 FPM for Laravel web applications',
	'php-81-laravel' => 'Bref PHP 8.1 for Laravel event driven applications',
	'php-81-fpm-laravel' => 'Bref PHP 8.1 FPM for Laravel web applications',
	'cli' => 'Bref Console runtime for Laravel web applications',
];

// Define the regions we want to publish to
$regions = [
    "us-west-2"
];

// Make sure the zip layers exist
foreach ($layers as $layer => $layerDescription) {
    $file = __DIR__ . "/export/$layer.zip";
    if (! file_exists($file)) {
        echo "File $file does not exist: generate the archives first\n";
        exit(1);
    }
}

// For each layer
foreach ($regions as $region) {

    // Initialize the lambda client
    $lambda = new LambdaClient([
        'region' => $region,
        'version' => 'latest',
    ]);

    // Get the layers to publish (cli and web)
    $layersToPublish = isset($argv[1]) ? [$argv[1] => $layers[$argv[1]]] : $layers;

    // For each layer
    foreach ($layersToPublish as $layer => $description) {

        // Get the file path
        $file = __DIR__ . "/export/$layer.zip";

        // Publish the later
        $publishResponse = $lambda->publishLayerVersion([
            'LayerName' => $layer,
            'Description' => $description,
            'LicenseInfo' => 'MIT',
            'CompatibleRuntimes' => ['provided.al2'],
            'Content' => [
                'ZipFile' => file_get_contents($file),
            ]
        ]);

        // Add the layer permissions
        $lambda->addLayerVersionPermission([
            'Action' => 'lambda:GetLayerVersion',
            'LayerName' => $layer,
            'Principal' => '*',
            'StatementId' => (string) time(),
            'VersionNumber' => (string) $publishResponse['Version'],
        ]);

        // Output the response
        echo '['.$region.']: '.$publishResponse['LayerVersionArn'].PHP_EOL;
    }
}