<?php declare(strict_types = 1);

namespace Tests\Cases;

require_once __DIR__ . '/../bootstrap.php';

use Contributte\OpenApi\Schema\OpenApi;
use Contributte\OpenApi\Validator\VersionValidator;
use Contributte\OpenApi\Version;
use Symfony\Component\Yaml\Yaml;
use Tester\Assert;
use Tester\TestCase;

/**
 * Proves the library supports each OpenAPI version it claims to support.
 *
 * Every supported version has one complete document - a document using every field that
 * version defines. Real-world examples only use the fields their authors needed, so they
 * cannot carry that proof; see ExamplesTest for those.
 */
final class VersionSupportTest extends TestCase
{

	private const DOCUMENT_DIRECTORY = __DIR__ . '/Schema/examples/';

	// Complete document of each supported version, as "version => file name".
	private const COMPLETE_DOCUMENTS = [
		Version::V3_0 => 'complete-3-0.yaml',
		Version::V3_1 => 'complete-3-1.yaml',
	];

	/**
	 * @return array<string, array{string, string}>
	 */
	public function provideCompleteDocuments(): array
	{
		$rows = [];

		foreach (self::COMPLETE_DOCUMENTS as $version => $file) {
			$rows[$version] = [$version, $file];
		}

		return $rows;
	}

	/**
	 * @dataProvider provideCompleteDocuments
	 */
	public function testCompleteDocument(string $version, string $file): void
	{
		$rawData = Yaml::parseFile(self::DOCUMENT_DIRECTORY . $file);

		$openApi = OpenApi::fromArray($rawData);

		self::assertSameDataStructure($rawData, $openApi->toArray(), $version);

		Assert::same(
			[],
			array_map(strval(...), (new VersionValidator())->validate($openApi)),
			sprintf('document of version %s raises no version problem', $version)
		);
	}

	/**
	 * @param mixed[] $expected
	 * @param mixed[] $actual
	 */
	private static function assertSameDataStructure(array $expected, array $actual, string $version): void
	{
		Assert::same(
			self::recursiveSort($expected),
			self::recursiveSort($actual),
			sprintf('document of version %s survives a round trip', $version)
		);
	}

	/**
	 * Key order carries no meaning in an OpenAPI document, so both sides are sorted before
	 * they are compared.
	 *
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

(new VersionSupportTest())->run();
