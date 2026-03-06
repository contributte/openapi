<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\MediaType;
use Contributte\OpenApi\Schema\Reference;
use Contributte\OpenApi\Schema\Schema;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test optional fields
Toolkit::test(static function (): void {
	$mediaType = new MediaType();
	$mediaType->setExample('whatever');

	$schema = new Schema([]);
	$mediaType->setSchema($schema);

	$realData = $mediaType->toArray();
	$expectedData = ['schema' => [], 'example' => 'whatever'];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, MediaType::fromArray($realData)->toArray());
});

// Test required fields
Toolkit::test(static function (): void {
	$mediaType = new MediaType();

	Assert::null($mediaType->getExample());
	Assert::null($mediaType->getSchema());

	$realData = $mediaType->toArray();
	$expectedData = [];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, MediaType::fromArray($realData)->toArray());
});

// Test schema reference
Toolkit::test(static function (): void {
	$mediaType = new MediaType();
	$schema = new Reference('ref');
	$mediaType->setSchema($schema);

	Assert::same($schema, $mediaType->getSchema());

	$realData = $mediaType->toArray();
	$expectedData = ['schema' => ['$ref' => 'ref']];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, MediaType::fromArray($realData)->toArray());
});
