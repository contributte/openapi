<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\PathItem;
use Contributte\OpenApi\Schema\Paths;
use Contributte\OpenApi\Schema\Reference;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class PathsTest extends TestCase
{

	private const ARRAY = [
		'/pets' => ['summary' => self::PETS_SUMMARY],
		'/pets/{petId}' => ['$ref' => self::PET_REFERENCE],
	];

	private const PETS_SUMMARY = 'Pet collection';

	private const PET_REFERENCE = '#/components/pathItems/Pet';

	private Paths $paths;

	public function testFromArray(): void
	{
		Assert::equal($this->paths, Paths::fromArray(self::ARRAY));
	}

	public function testToArray(): void
	{
		Assert::same(self::ARRAY, $this->paths->toArray());
	}

	public function testVendorExtensions(): void
	{
		$expectedData = self::ARRAY + ['x-internal-id' => 42];

		$paths = Paths::fromArray($expectedData);

		Assert::same(42, $paths->getVendorExtensions()?->getExtension('x-internal-id'));
		Assert::null($paths->getPath('x-internal-id'));
		Assert::same($expectedData, $paths->toArray());
	}

	protected function setUp(): void
	{
		$pathItem = new PathItem();
		$pathItem->setSummary(self::PETS_SUMMARY);

		$this->paths = new Paths();
		$this->paths->setPathItem('/pets', $pathItem);
		$this->paths->setPathItem('/pets/{petId}', new Reference(self::PET_REFERENCE));
	}

}

(new PathsTest())->run();
