<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

require_once __DIR__ . '/../../bootstrap.php';

use Contributte\OpenApi\Schema\OpenApi;
use Symfony\Component\Yaml\Yaml;
use Tester\Assert;
use Tester\TestCase;

/**
 * Tests of examples from openapi specification https://github.com/OAI/OpenAPI-Specification/tree/master/examples/v3.0
 */
final class ExamplesTest extends TestCase
{

	public function testApiWithExamples(): void
	{
		$rawData = Yaml::parseFile(__DIR__ . '/examples/api-with-examples.yaml');
		$openApi = OpenApi::fromArray($rawData);
		$openApiData = $openApi->toArray();
		self::assertSameDataStructure($rawData, $openApiData);
	}

	public function testCallbackExample(): void
	{
		$rawData = Yaml::parseFile(__DIR__ . '/examples/callback-example.yaml');
		$openApi = OpenApi::fromArray($rawData);
		$openApiData = $openApi->toArray();
		self::assertSameDataStructure($rawData, $openApiData);
	}

	public function testLinkExample(): void
	{
		$rawData = Yaml::parseFile(__DIR__ . '/examples/link-example.yaml');
		$openApi = OpenApi::fromArray($rawData);
		$openApiData = $openApi->toArray();
		self::assertSameDataStructure($rawData, $openApiData);
	}

	public function testGitLab(): void
	{
		$rawData = Yaml::parseFile(__DIR__ . '/examples/gitlab.yaml');
		$openApi = OpenApi::fromArray($rawData);
		$openApiData = $openApi->toArray();
		self::assertSameDataStructure($rawData, $openApiData);
	}

	public function testPetstore(): void
	{
		$rawData = Yaml::parseFile(__DIR__ . '/examples/petstore.yaml');
		$openApi = OpenApi::fromArray($rawData);
		$openApiData = $openApi->toArray();
		self::assertSameDataStructure($rawData, $openApiData);
	}

	public function testPetstoreExpanded(): void
	{
		$rawData = Yaml::parseFile(__DIR__ . '/examples/petstore-expanded.yaml');
		$openApi = OpenApi::fromArray($rawData);
		$openApiData = $openApi->toArray();
		self::assertSameDataStructure($rawData, $openApiData);
	}

	public function testUspto(): void
	{
		$rawData = Yaml::parseFile(__DIR__ . '/examples/uspto.yaml');
		$openApi = OpenApi::fromArray($rawData);
		$openApiData = $openApi->toArray();
		self::assertSameDataStructure($rawData, $openApiData);
	}

	public function testWebhooks(): void
	{
		$rawData = Yaml::parseFile(__DIR__ . '/examples/webhook-example.yaml');
		$openApi = OpenApi::fromArray($rawData);
		$openApiData = $openApi->toArray();
		self::assertSameDataStructure($rawData, $openApiData);
	}

	public function testNonOauthScopes(): void
	{
		$rawData = Yaml::parseFile(__DIR__ . '/examples/non-oauth-scopes.yaml');
		$openApi = OpenApi::fromArray($rawData);
		$openApiData = $openApi->toArray();
		self::assertSameDataStructure($rawData, $openApiData);
	}

	public function testRedoclyMuseum(): void
	{
		$rawData = Yaml::parseFile(__DIR__ . '/examples/redocly-museum.yaml');
		$openApi = OpenApi::fromArray($rawData);
		$openApiData = $openApi->toArray();
		self::assertSameDataStructure($rawData, $openApiData);
	}

	/**
	 * @param mixed[] $expected
	 * @param mixed[] $actual
	 */
	private static function assertSameDataStructure(array $expected, array $actual): void
	{
		$expected = self::recursiveSort($expected);
		$actual = self::recursiveSort($actual);
		Assert::same($expected, $actual);
	}

	/**
	 * @param mixed[] $data
	 * @return mixed[]
	 */
	private static function recursiveSort(array $data): array
	{
		foreach ($data as $key => $value) {
			if (!is_array($value)) {
				continue;
			}

			$data[$key] = self::recursiveSort($value);
		}

		unset($value);
		ksort($data);

		return $data;
	}

}

(new ExamplesTest())->run();
