<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\ServerVariable;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test optional fields
Toolkit::test(static function (): void {
	$variable = new ServerVariable('default');
	$variable->setDescription('description');
	$variable->setEnum(['foo', 'bar', 'baz']);

	Assert::same('default', $variable->getDefault());
	Assert::same('description', $variable->getDescription());
	Assert::same(['foo', 'bar', 'baz'], $variable->getEnum());

	$realData = $variable->toArray();
	$expectedData = [
		'enum' => ['foo', 'bar', 'baz'],
		'default' => 'default',
		'description' => 'description',
	];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, ServerVariable::fromArray($realData)->toArray());
});

// Test required fields
Toolkit::test(static function (): void {
	$variable = new ServerVariable('default');

	Assert::same('default', $variable->getDefault());
	Assert::null($variable->getDescription());
	Assert::same([], $variable->getEnum());

	$realData = $variable->toArray();
	$expectedData = ['default' => 'default'];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, ServerVariable::fromArray($realData)->toArray());
});
