<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\MediaType;
use Contributte\OpenApi\Schema\RequestBody;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test optional fields
Toolkit::test(static function (): void {
	$body = new RequestBody();

	$content = [];
	$content['text/*'] = $mediaType1 = new MediaType();
	$body->addMediaType('text/*', $mediaType1);
	$body->addMediaType('application/json', $mediaType1); // Intentionally added twice, tests overriding
	$content['application/json'] = $mediaType2 = new MediaType();
	$body->addMediaType('application/json', $mediaType2);

	$body->setDescription('description');
	$body->setRequired(true);

	Assert::same($content, $body->getContent());
	Assert::same('description', $body->getDescription());
	Assert::true($body->isRequired());

	$realData = $body->toArray();
	$expectedData = [
		'description' => 'description',
		'content' => ['text/*' => [], 'application/json' => []],
		'required' => true,
	];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, RequestBody::fromArray($realData)->toArray());
});

// Test required fields
Toolkit::test(static function (): void {
	$body = new RequestBody();

	Assert::same([], $body->getContent());
	Assert::null($body->getDescription());
	Assert::false($body->isRequired());

	$realData = $body->toArray();
	$expectedData = ['content' => []];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, RequestBody::fromArray($realData)->toArray());
});
