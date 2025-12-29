<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\Reference;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test optional fields
Toolkit::test(static function (): void {
	$ref = new Reference('#/components/responses/ServerError');
	$ref->setSummary('Server error');
	$ref->setDescription('Server error response, e.g. unhandled exception');

	Assert::same('#/components/responses/ServerError', $ref->getRef());
	Assert::same('Server error', $ref->getSummary());
	Assert::same('Server error response, e.g. unhandled exception', $ref->getDescription());

	$realData = $ref->toArray();
	$expectedData = [
		'$ref' => '#/components/responses/ServerError',
		'summary' => 'Server error',
		'description' => 'Server error response, e.g. unhandled exception',
	];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, Reference::fromArray($realData)->toArray());
});

// Test required fields
Toolkit::test(static function (): void {
	$ref = new Reference('#/components/responses/ServerError');

	Assert::same('#/components/responses/ServerError', $ref->getRef());
	Assert::null($ref->getSummary());
	Assert::null($ref->getDescription());

	$realData = $ref->toArray();
	$expectedData = ['$ref' => '#/components/responses/ServerError'];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, Reference::fromArray($realData)->toArray());
});
