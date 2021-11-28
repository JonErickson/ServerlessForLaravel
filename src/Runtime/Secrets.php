<?php

namespace JonErickson\ServerlessForLaravel\Runtime;

use Aws\Ssm\SsmClient;

class Secrets
{
    /**
     * Add all of the secret parameters at the given path to the environment.
     *
     * @param $path
     *
     * @return mixed
     */
    public static function addToEnvironment($path)
    {
        // Get all env vars from SSM and add them to our environment
        return tap(
            static::all($path),
            function ($variables) {
                foreach ($variables as $key => $value) {
                    fwrite(STDERR, "> Environment Bootstrap: Adding $key".PHP_EOL);
                    $_ENV[$key] = $value;
                }
            }
        );
    }

    /**
     * Get all of the secret parameters (AWS SSM) at the given path.
     *
     * @param $path
     *
     * @return array
     */
    protected static function all($path)
    {
        // Initialize SSM client
        $ssm = SsmClient::factory(
            [
            'region' => $_ENV['AWS_DEFAULT_REGION'],
            'version' => 'latest',
            ]
        );

        // Fetch the parameters
        $results = $ssm->getPaginator(
            'GetParametersByPath',
            [
            'Path' => $path,
            'WithDecryption' => true,
            ]
        );

        // Parse the secrets
        $secrets = [];
        foreach ($results as $result) {
            $secrets = array_merge(
                static::parseSecrets(
                    $path,
                    $result['Parameters'] ?? []
                ),
                $secrets
            );
        }

        // Return the secrets
        return $secrets;
    }

    /**
     * Parse the secret names and values into an array.
     *
     * @param $path
     * @param array $secrets
     *
     * @return mixed
     */
    protected static function parseSecrets($path, array $secrets)
    {
        // Parse the parameters into Laravel env vars
        return collect($secrets)->mapWithKeys(
            function ($secret) use ($path) {
                $segments = explode('/', strtoupper(str_replace($path, '', $secret['Name'])));
                return [$segments[count($segments) - 1] => $secret['Value']];
            }
        )->all();
    }
}
