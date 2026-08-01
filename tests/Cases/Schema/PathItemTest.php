<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

require_once __DIR__ . '/../../bootstrap.php';

use Contributte\OpenApi\Schema\PathItem;
use Tester\Assert;
use Tester\TestCase;

class PathItemTest extends TestCase
{

	private const RESPONSES = ['responses' => ['200' => ['description' => 'Success']]];

	public function testOperations(): void
	{
		$expectedData = [
			'get' => self::RESPONSES,
			'post' => self::RESPONSES,
		];

		Assert::same($expectedData, PathItem::fromArray($expectedData)->toArray());
	}

	public function testQueryOperation(): void
	{
		$expectedData = [
			'get' => self::RESPONSES,
			'query' => self::RESPONSES,
		];

		Assert::same($expectedData, PathItem::fromArray($expectedData)->toArray());
	}

}

(new PathItemTest())->run();
