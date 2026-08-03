<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Validator;

use Contributte\OpenApi\Schema\OpenApi;
use Contributte\OpenApi\Schema\SecurityScheme;
use Contributte\OpenApi\Version;

/**
 * Reports constructs that do not belong to the version the document declares.
 *
 * Every rule is a table entry of "document path => version", so supporting a new
 * OpenAPI version means adding rows, not methods.
 *
 * Problems are returned, never thrown: a library has no control over the documents
 * handed to it, and refusing to parse is worse than reporting.
 */
class VersionValidator
{

	/**
	 * Fields and the version that introduced them. A "*" segment matches any key
	 * of a map.
	 */
	public const FIELD_INTRODUCED_IN = [
		'jsonSchemaDialect' => Version::V3_1,
		'webhooks' => Version::V3_1,
		'info.summary' => Version::V3_1,
		'info.license.identifier' => Version::V3_1,
		'components.pathItems' => Version::V3_1,
	];

	/**
	 * Field values and the version that introduced them, as
	 * "document path => [value => version]".
	 */
	public const VALUE_INTRODUCED_IN = [
		'components.securitySchemes.*.type' => [
			SecurityScheme::TYPE_MUTUAL_TLS => Version::V3_1,
		],
	];

	/**
	 * Required fields and the version that made them optional.
	 */
	private const FIELD_OPTIONAL_SINCE = [
		'paths' => Version::V3_1,
	];

	/**
	 * @return Problem[]
	 */
	public function validate(OpenApi $openApi): array
	{
		$data = $openApi->toArray();
		$version = is_string($data['openapi'] ?? null) ? $data['openapi'] : '';
		$problems = [];

		if ($version === '') {
			$problems[] = new Problem(
				Problem::LEVEL_WARNING,
				'openapi',
				sprintf('No OpenAPI version declared. Reading the document as %s.', Version::DEFAULT)
			);

			$version = Version::DEFAULT;
		} elseif (!Version::isSupported($version)) {
			return [
				new Problem(
					Problem::LEVEL_WARNING,
					'openapi',
					sprintf(
						'Unsupported OpenAPI version "%s". Supported versions are "%s".',
						$version,
						implode('", "', Version::SUPPORTED)
					)
				),
			];
		}

		foreach (self::FIELD_INTRODUCED_IN as $path => $introducedIn) {
			if (!Version::isBefore($version, $introducedIn)) {
				continue;
			}

			foreach (array_keys(self::collect($data, $path)) as $foundPath) {
				$problems[] = self::introducedIn($foundPath, $introducedIn, $version);
			}
		}

		foreach (self::VALUE_INTRODUCED_IN as $path => $values) {
			foreach (self::collect($data, $path) as $foundPath => $foundValue) {
				foreach ($values as $value => $introducedIn) {
					if ($foundValue === $value && Version::isBefore($version, $introducedIn)) {
						$problems[] = self::introducedIn($foundPath, $introducedIn, $version);
					}
				}
			}
		}

		foreach (self::FIELD_OPTIONAL_SINCE as $path => $optionalSince) {
			if (!Version::isBefore($version, $optionalSince) || self::collect($data, $path) !== []) {
				continue;
			}

			$problems[] = new Problem(
				Problem::LEVEL_ERROR,
				$path,
				sprintf(
					'Attribute "%s" is required in OpenAPI %s. It became optional in %s.',
					$path,
					$version,
					$optionalSince
				)
			);
		}

		return $problems;
	}

	private static function introducedIn(string $path, string $introducedIn, string $version): Problem
	{
		return new Problem(
			Problem::LEVEL_WARNING,
			$path,
			sprintf(
				'Attribute "%s" was introduced in OpenAPI %s, but the document declares %s.',
				$path,
				$introducedIn,
				$version
			)
		);
	}

	/**
	 * Resolves a dot-separated path, expanding "*" into every key of the map it
	 * stands for, and returns the concrete paths that exist mapped to their values.
	 *
	 * @param mixed[] $data
	 * @return array<string, mixed>
	 */
	private static function collect(array $data, string $path): array
	{
		$found = ['' => $data];

		foreach (explode('.', $path) as $segment) {
			$next = [];

			foreach ($found as $prefix => $value) {
				if (!is_array($value)) {
					continue;
				}

				$keys = $segment === '*' ? array_keys($value) : [$segment];

				foreach ($keys as $key) {
					if (!array_key_exists($key, $value)) {
						continue;
					}

					$next[$prefix === '' ? (string) $key : $prefix . '.' . $key] = $value[$key];
				}
			}

			$found = $next;
		}

		return $found;
	}

}
