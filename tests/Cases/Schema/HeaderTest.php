<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

require_once __DIR__ . '/../../bootstrap.php';

use Contributte\OpenApi\Schema\Header;
use Tester\Assert;
use Tester\TestCase;

class HeaderTest extends TestCase
{

	public function testSchema(): void
	{
		$expectedData = [
			'description' => 'The number of allowed requests in the current period',
			'schema' => ['type' => 'integer'],
		];

		Assert::same($expectedData, Header::fromArray($expectedData)->toArray());
	}

	public function testContent(): void
	{
		$expectedData = [
			'description' => 'A structured header',
			'content' => [
				'application/json' => ['schema' => ['type' => 'object']],
			],
		];

		Assert::same($expectedData, Header::fromArray($expectedData)->toArray());
	}

}

(new HeaderTest())->run();
