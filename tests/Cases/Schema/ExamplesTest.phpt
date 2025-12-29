<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\OpenApi;
use Contributte\Tester\Toolkit;
use Symfony\Component\Yaml\Yaml;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @param mixed[] $data
 * @return mixed[]
 */
function recursiveSort(array $data): array
{
	foreach ($data as $key => $value) {
		if (!is_array($value)) {
			continue;
		}

		$data[$key] = recursiveSort($value);
	}

	unset($value);
	ksort($data);

	return $data;
}

/**
 * @param mixed[] $expected
 * @param mixed[] $actual
 */
function assertSameDataStructure(array $expected, array $actual): void
{
	$expected = recursiveSort($expected);
	$actual = recursiveSort($actual);
	Assert::same($expected, $actual);
}

// Test api-with-examples.yaml
Toolkit::test(static function (): void {
	$rawData = Yaml::parseFile(__DIR__ . '/examples/api-with-examples.yaml');
	$openApi = OpenApi::fromArray($rawData);
	$openApiData = $openApi->toArray();
	assertSameDataStructure($rawData, $openApiData);
});

// Test callback-example.yaml
Toolkit::test(static function (): void {
	$rawData = Yaml::parseFile(__DIR__ . '/examples/callback-example.yaml');
	$openApi = OpenApi::fromArray($rawData);
	$openApiData = $openApi->toArray();
	assertSameDataStructure($rawData, $openApiData);
});

// Test link-example.yaml
Toolkit::test(static function (): void {
	$rawData = Yaml::parseFile(__DIR__ . '/examples/link-example.yaml');
	$openApi = OpenApi::fromArray($rawData);
	$openApiData = $openApi->toArray();
	assertSameDataStructure($rawData, $openApiData);
});

// Test gitlab.yaml
Toolkit::test(static function (): void {
	$rawData = Yaml::parseFile(__DIR__ . '/examples/gitlab.yaml');
	$openApi = OpenApi::fromArray($rawData);
	$openApiData = $openApi->toArray();
	assertSameDataStructure($rawData, $openApiData);
});

// Test petstore.yaml
Toolkit::test(static function (): void {
	$rawData = Yaml::parseFile(__DIR__ . '/examples/petstore.yaml');
	$openApi = OpenApi::fromArray($rawData);
	$openApiData = $openApi->toArray();
	assertSameDataStructure($rawData, $openApiData);
});

// Test petstore-expanded.yaml
Toolkit::test(static function (): void {
	$rawData = Yaml::parseFile(__DIR__ . '/examples/petstore-expanded.yaml');
	$openApi = OpenApi::fromArray($rawData);
	$openApiData = $openApi->toArray();
	assertSameDataStructure($rawData, $openApiData);
});

// Test uspto.yaml
Toolkit::test(static function (): void {
	$rawData = Yaml::parseFile(__DIR__ . '/examples/uspto.yaml');
	$openApi = OpenApi::fromArray($rawData);
	$openApiData = $openApi->toArray();
	assertSameDataStructure($rawData, $openApiData);
});

// Test webhook-example.yaml
Toolkit::test(static function (): void {
	$rawData = Yaml::parseFile(__DIR__ . '/examples/webhook-example.yaml');
	$openApi = OpenApi::fromArray($rawData);
	$openApiData = $openApi->toArray();
	assertSameDataStructure($rawData, $openApiData);
});

// Test non-oauth-scopes.yaml
Toolkit::test(static function (): void {
	$rawData = Yaml::parseFile(__DIR__ . '/examples/non-oauth-scopes.yaml');
	$openApi = OpenApi::fromArray($rawData);
	$openApiData = $openApi->toArray();
	assertSameDataStructure($rawData, $openApiData);
});

// Test redocly-museum.yaml
Toolkit::test(static function (): void {
	$rawData = Yaml::parseFile(__DIR__ . '/examples/redocly-museum.yaml');
	$openApi = OpenApi::fromArray($rawData);
	$openApiData = $openApi->toArray();
	assertSameDataStructure($rawData, $openApiData);
});
