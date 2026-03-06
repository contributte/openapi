<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\Reference;
use Contributte\OpenApi\Schema\Response;
use Contributte\OpenApi\Schema\Responses;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test fromArray
Toolkit::test(static function (): void {
	$array = [
		'200' => ['description' => 'Success'],
		'401' => ['$ref' => '#/components/responses/UnauthorizedError'],
	];

	$responses = new Responses();
	$responses->setResponse('200', new Response('Success'));
	$responses->setResponse('401', new Reference('#/components/responses/UnauthorizedError'));

	$actual = Responses::fromArray($array);
	Assert::equal($responses, $actual);
});

// Test toArray
Toolkit::test(static function (): void {
	$array = [
		'200' => ['description' => 'Success'],
		'401' => ['$ref' => '#/components/responses/UnauthorizedError'],
	];

	$responses = new Responses();
	$responses->setResponse('200', new Response('Success'));
	$responses->setResponse('401', new Reference('#/components/responses/UnauthorizedError'));

	Assert::equal($array, $responses->toArray());
});
