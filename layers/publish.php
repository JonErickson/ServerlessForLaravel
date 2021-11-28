<?php

use Aws\Lambda\LambdaClient;
use Symfony\Component\Process\Process;

require_once __DIR__ . '/../vendor/autoload.php';

// Define our layer names and descriptions
$layers = [
    'serverless-for-laravel-cli' => 'Laravel Bootstrap for Bref running in Console',
    'serverless-for-laravel-web' => 'Laravel Bootstrap for Bref running in FPM',
];

/// Define the regions we want to publish to
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
    $lambda = new LambdaClient(
        [
        'region' => $region,
        'version' => 'latest',
        ]
    );

    // Get the layers to publish (cli and web)
    $layersToPublish = isset($argv[1]) ? [$argv[1] => $layers[$argv[1]]] : $layers;

    // For each layer
    foreach ($layersToPublish as $layer => $description) {

        // Get the file path
        $file = __DIR__ . "/export/$layer.zip";

        // Publish the later
        $publishResponse = $lambda->publishLayerVersion(
            [
            'LayerName' => $layer,
            'Description' => $description,
            'LicenseInfo' => 'MIT',
            'Content' => [
            'ZipFile' => file_get_contents($file),
            ],
            ]
        );

        // Add the layer permissions
        $lambda->addLayerVersionPermission(
            [
            'Action' => 'lambda:GetLayerVersion',
            'LayerName' => $layer,
            'Principal' => '*',
            'StatementId' => (string) time(),
            'VersionNumber' => (string) $publishResponse['Version'],
            ]
        );

        // Output the response
        echo '['.$region.']: '.$publishResponse['LayerVersionArn'].PHP_EOL;
    }
}