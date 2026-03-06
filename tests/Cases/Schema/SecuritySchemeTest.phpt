<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\OAuthFlow;
use Contributte\OpenApi\Schema\SecurityScheme;
use Contributte\Tester\Toolkit;
use InvalidArgumentException;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test required fields - API Key
Toolkit::test(static function (): void {
	$array = [
		'type' => SecurityScheme::TYPE_API_KEY,
		'name' => 'api_key',
		'in' => SecurityScheme::IN_HEADER,
	];
	$securityScheme = SecurityScheme::fromArray($array);
	Assert::same($array, $securityScheme->toArray());
});

// Test required fields - HTTP basic
Toolkit::test(static function (): void {
	$array = [
		'type' => SecurityScheme::TYPE_HTTP,
		'scheme' => 'basic',
	];
	$securityScheme = SecurityScheme::fromArray($array);
	Assert::same($array, $securityScheme->toArray());
});

// Test required fields - HTTP bearer
Toolkit::test(static function (): void {
	$array = [
		'type' => SecurityScheme::TYPE_HTTP,
		'scheme' => 'bearer',
		'bearerFormat' => 'JWT',
	];
	$securityScheme = SecurityScheme::fromArray($array);
	Assert::same($array, $securityScheme->toArray());
});

// Test required fields - OAuth2
Toolkit::test(static function (): void {
	$array = [
		'type' => SecurityScheme::TYPE_OAUTH2,
		'flows' => [
			'implicit' => [
				'authorizationUrl' => 'https://example.com/authorization',
				'tokenUrl' => 'https://example.com/token',
				'refreshUrl' => 'https://example.com/refresh',
				'scopes' => ['read' => 'Read access', 'write' => 'Write access'],
			],
			'password' => [
				'authorizationUrl' => 'https://example.com/authorization',
				'tokenUrl' => 'https://example.com/token',
				'refreshUrl' => 'https://example.com/refresh',
				'scopes' => ['read' => 'Read access', 'write' => 'Write access'],
			],
			'clientCredentials' => [
				'authorizationUrl' => 'https://example.com/authorization',
				'tokenUrl' => 'https://example.com/token',
				'refreshUrl' => 'https://example.com/refresh',
				'scopes' => ['read' => 'Read access', 'write' => 'Write access'],
			],
			'authorizationCode' => [
				'authorizationUrl' => 'https://example.com/authorization',
				'tokenUrl' => 'https://example.com/token',
				'refreshUrl' => 'https://example.com/refresh',
				'scopes' => ['read' => 'Read access', 'write' => 'Write access'],
			],
		],
	];
	$securityScheme = SecurityScheme::fromArray($array);
	Assert::same($array, $securityScheme->toArray());
});

// Test required fields - OpenID Connect
Toolkit::test(static function (): void {
	$array = [
		'type' => SecurityScheme::TYPE_OPEN_ID_CONNECT,
		'openIdConnectUrl' => 'https://example.com/.well-known/openid-configuration',
	];
	$securityScheme = SecurityScheme::fromArray($array);
	Assert::same($array, $securityScheme->toArray());
});

// Test optional fields
Toolkit::test(static function (): void {
	$type = SecurityScheme::TYPE_API_KEY;
	$name = 'api_key';
	$in = SecurityScheme::IN_HEADER;
	$description = 'API key';
	$securityScheme = new SecurityScheme($type);
	$securityScheme->setName($name);
	$securityScheme->setIn($in);
	$securityScheme->setDescription($description);

	Assert::same($type, $securityScheme->getType());
	Assert::same($name, $securityScheme->getName());
	Assert::same($in, $securityScheme->getIn());
	Assert::same($description, $securityScheme->getDescription());

	$array = $securityScheme->toArray();
	$expected = [
		'type' => $type,
		'name' => $name,
		'description' => $description,
		'in' => $in,
	];
	Assert::same($expected, $array);
	Assert::same($expected, SecurityScheme::fromArray($array)->toArray());
});

// Test invalid type
Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		new SecurityScheme('invalid');
	}, InvalidArgumentException::class, 'Invalid value "invalid" for attribute "type" given. It must be one of "apiKey, http, mutualTLS, oauth2, openIdConnect".');
});

// Test missing name
Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		$securityScheme = new SecurityScheme(SecurityScheme::TYPE_API_KEY);
		$securityScheme->setIn(SecurityScheme::IN_HEADER);
		$securityScheme->setName(null);
	}, InvalidArgumentException::class, 'Attribute "name" is required for type "apiKey".');
});

// Test missing in
Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		$securityScheme = new SecurityScheme(SecurityScheme::TYPE_API_KEY);
		$securityScheme->setName('api_key');
		$securityScheme->setIn(null);
	}, InvalidArgumentException::class, 'Attribute "in" is required for type "apiKey".');
});

// Test invalid in
Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		$securityScheme = new SecurityScheme(SecurityScheme::TYPE_API_KEY);
		$securityScheme->setName('api_key');
		$securityScheme->setIn('invalid');
	}, InvalidArgumentException::class, 'Invalid value "invalid" for attribute "in" given. It must be one of "cookie, header, query".');
});

// Test missing scheme
Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		$securityScheme = new SecurityScheme(SecurityScheme::TYPE_HTTP);
		$securityScheme->setScheme(null);
	}, InvalidArgumentException::class, 'Attribute "scheme" is required for type "http".');
});

// Test missing bearer format
Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		$securityScheme = new SecurityScheme(SecurityScheme::TYPE_HTTP);
		$securityScheme->setScheme('bearer');
		$securityScheme->setBearerFormat(null);
	}, InvalidArgumentException::class, 'Attribute "bearerFormat" is required for type "http" and scheme "bearer".');
});

// Test missing flows
Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		$securityScheme = new SecurityScheme(SecurityScheme::TYPE_OAUTH2);
		$securityScheme->setFlows([]);
	}, InvalidArgumentException::class, 'Attribute "flows" is required for type "oauth2".');
});

// Test missing flow
Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		$securityScheme = new SecurityScheme(SecurityScheme::TYPE_OAUTH2);
		$securityScheme->setFlows([
			'implicit' => OAuthFlow::fromArray([
				'authorizationUrl' => 'https://example.com/authorization',
				'tokenUrl' => 'https://example.com/token',
				'refreshUrl' => 'https://example.com/refresh',
				'scopes' => ['read' => 'Read access', 'write' => 'Write access'],
			]),
		]);
	}, InvalidArgumentException::class, 'Attribute "flows" is missing required key "password".');
});

// Test missing openIdConnectUrl
Toolkit::test(static function (): void {
	Assert::exception(static function (): void {
		$securityScheme = new SecurityScheme(SecurityScheme::TYPE_OPEN_ID_CONNECT);
		$securityScheme->setOpenIdConnectUrl(null);
	}, InvalidArgumentException::class, 'Attribute "openIdConnectUrl" is required for type "openIdConnect".');
});
