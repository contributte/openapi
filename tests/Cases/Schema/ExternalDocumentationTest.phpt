<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\ExternalDocumentation;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test optional fields
Toolkit::test(static function (): void {
	$documentation = new ExternalDocumentation('https://example.com');
	$documentation->setDescription('Find more info here');

	Assert::same('https://example.com', $documentation->getUrl());
	Assert::same('Find more info here', $documentation->getDescription());

	$realData = $documentation->toArray();
	$expectedData = [
		'url' => 'https://example.com',
		'description' => 'Find more info here',
	];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, ExternalDocumentation::fromArray($realData)->toArray());
});

// Test required fields
Toolkit::test(static function (): void {
	$documentation = new ExternalDocumentation('https://example.com');

	Assert::same('https://example.com', $documentation->getUrl());
	Assert::null($documentation->getDescription());

	$realData = $documentation->toArray();
	$expectedData = ['url' => 'https://example.com'];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, ExternalDocumentation::fromArray($realData)->toArray());
});
