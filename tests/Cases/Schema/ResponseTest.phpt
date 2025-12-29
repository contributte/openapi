<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\Header;
use Contributte\OpenApi\Schema\Reference;
use Contributte\OpenApi\Schema\Response;
use Contributte\OpenApi\Schema\Schema;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test optional fields
Toolkit::test(static function (): void {
	$array = [
		'description' => 'Description',
		'headers' => [
			'WWW-Authenticate' => [
				'description' => 'The authentication method that should be used to gain access to a resource',
				'schema' => ['type' => 'string'],
			],
		],
	];
	$response = new Response('Description');
	$header = new Header();
	$header->setDescription('The authentication method that should be used to gain access to a resource');
	$headerSchema = new Schema(['type' => 'string']);
	$header->setSchema($headerSchema);
	$response->setHeader('WWW-Authenticate', $header);
	Assert::same($array, $response->toArray());
	Assert::equal($response, Response::fromArray($array));
});

// Test required fields
Toolkit::test(static function (): void {
	$array = ['description' => 'Description'];
	$response = new Response('Description');
	Assert::same($array, $response->toArray());
	Assert::equal($response, Response::fromArray($array));
});

// Test header reference
Toolkit::test(static function (): void {
	$array = [
		'description' => 'API key or user token is missing or invalid',
		'headers' => [
			'WWW-Authenticate' => [
				'$ref' => '#/components/header/WWW-Authenticate',
			],
		],
	];
	$response = new Response('API key or user token is missing or invalid');
	$headerReference = new Reference('#/components/header/WWW-Authenticate');
	$response->setHeader('WWW-Authenticate', $headerReference);
	Assert::same($array, $response->toArray());
	Assert::equal($response, Response::fromArray($array));
});
