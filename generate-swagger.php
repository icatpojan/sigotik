<?php

/**
 * Generate Swagger Documentation for Sigotik API
 *
 * This script generates the Swagger/OpenAPI documentation
 * for the Sigotik API endpoints.
 */

require_once __DIR__ . '/vendor/autoload.php';

use OpenApi\Generator;
use OpenApi\Util;

// Set the base path for annotations
$paths = [
    __DIR__ . '/app/Http/Controllers/Api',
    __DIR__ . '/app/Http/Controllers',
];

// Generate the OpenAPI specification
$openapi = Generator::scan($paths, [
    'exclude' => [
        __DIR__ . '/app/Http/Controllers/Api',
    ],
]);

// Output the JSON
file_put_contents(__DIR__ . '/public/api-docs.json', $openapi->toJson());

// Output the YAML
file_put_contents(__DIR__ . '/public/api-docs.yaml', $openapi->toYaml());

echo "Swagger documentation generated successfully!\n";
echo "JSON: public/api-docs.json\n";
echo "YAML: public/api-docs.yaml\n";
echo "View at: http://localhost:8000/api/documentation\n";
