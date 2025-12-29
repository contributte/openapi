<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\ExternalDocumentation;
use Contributte\OpenApi\Schema\Tag;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test optional fields
Toolkit::test(static function (): void {
	$tag = new Tag('pet');
	$tag->setDescription('Pets operations');

	$externalDocs = new ExternalDocumentation('https://example.com');
	$tag->setExternalDocs($externalDocs);

	Assert::same('pet', $tag->getName());
	Assert::same('Pets operations', $tag->getDescription());
	Assert::same($externalDocs, $tag->getExternalDocs());

	$realData = $tag->toArray();
	$expectedData = [
		'name' => 'pet',
		'description' => 'Pets operations',
		'externalDocs' => ['url' => 'https://example.com'],
	];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, Tag::fromArray($realData)->toArray());
});

// Test required fields
Toolkit::test(static function (): void {
	$tag = new Tag('pet');

	Assert::same('pet', $tag->getName());
	Assert::null($tag->getDescription());
	Assert::null($tag->getExternalDocs());

	$realData = $tag->toArray();
	$expectedData = ['name' => 'pet'];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, Tag::fromArray($realData)->toArray());
});
