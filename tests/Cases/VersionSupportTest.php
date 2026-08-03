<?php declare(strict_types = 1);

namespace Tests\Cases;

require_once __DIR__ . '/../bootstrap.php';

use Contributte\OpenApi\Schema\OpenApi;
use Contributte\OpenApi\Validator\Problem;
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

		$expectedPaths = self::expectedPaths($version);

		if ($expectedPaths === []) {
			return;
		}

		$rawData['openapi'] = Version::SUPPORTED[0];
		$reported = array_map(
			static fn (Problem $problem): string => $problem->getPath(),
			(new VersionValidator())->validate(OpenApi::fromArray($rawData))
		);

		$uncovered = array_values(array_filter(
			$expectedPaths,
			static function (string $pattern) use ($reported): bool {
				foreach ($reported as $path) {
					if (self::matchesPath($pattern, $path)) {
						return false;
					}
				}

				return true;
			}
		));

		Assert::same(
			[],
			$uncovered,
			sprintf('document of version %s exercises every field that version introduced', $version)
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

	/**
	 * Rule paths a document of this version must exercise: everything introduced after the
	 * oldest supported version, up to and including this one. The validator's tables are the
	 * list of what the library knows about versions, so they double as the coverage list.
	 *
	 * Both tables contribute "(path, introducedIn)" pairs to a flat list rather than a
	 * path-keyed map, so a path that carries several values introduced in different versions
	 * cannot have one overwrite another before the version filter runs.
	 *
	 * @return string[]
	 */
	private static function expectedPaths(string $version): array
	{
		$oldest = Version::SUPPORTED[0];

		$pairs = [];

		foreach (VersionValidator::FIELD_INTRODUCED_IN as $path => $introduced) {
			$pairs[] = [$path, $introduced];
		}

		foreach (VersionValidator::VALUE_INTRODUCED_IN as $path => $values) {
			foreach ($values as $introduced) {
				$pairs[] = [$path, $introduced];
			}
		}

		$paths = [];

		foreach ($pairs as [$path, $introduced]) {
			if (Version::isBefore($oldest, $introduced) && !Version::isBefore($version, $introduced)) {
				$paths[$path] = true;
			}
		}

		$paths = array_keys($paths);
		sort($paths);

		return $paths;
	}

	/**
	 * Whether a concrete document path matches a rule path, whose "*" segment stands for any
	 * single key of a map.
	 */
	private static function matchesPath(string $pattern, string $path): bool
	{
		$regex = '#^' . str_replace('\*', '[^.]+', preg_quote($pattern, '#')) . '$#';

		return preg_match($regex, $path) === 1;
	}

}

(new VersionSupportTest())->run();
