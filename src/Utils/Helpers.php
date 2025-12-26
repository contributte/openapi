<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Utils;

class Helpers
{

	/**
	 * @param array<mixed> $data
	 */
	public static function getString(array $data, string $key): string
	{
		$value = $data[$key] ?? null;

		if (!is_string($value)) {
			throw new \InvalidArgumentException(sprintf('Key "%s" must be a string', $key));
		}

		return $value;
	}

	/**
	 * @param array<mixed> $data
	 */
	public static function getStringOrNull(array $data, string $key): ?string
	{
		$value = $data[$key] ?? null;

		if ($value === null) {
			return null;
		}

		if (!is_string($value)) {
			throw new \InvalidArgumentException(sprintf('Key "%s" must be a string or null', $key));
		}

		return $value;
	}

	/**
	 * @param array<mixed> $data
	 * @return array<mixed>
	 */
	public static function getArray(array $data, string $key): array
	{
		$value = $data[$key] ?? null;

		if (!is_array($value)) {
			throw new \InvalidArgumentException(sprintf('Key "%s" must be an array', $key));
		}

		return $value;
	}

	/**
	 * @param array<mixed> $data
	 * @return array<mixed>|null
	 */
	public static function getArrayOrNull(array $data, string $key): ?array
	{
		$value = $data[$key] ?? null;

		if ($value === null) {
			return null;
		}

		if (!is_array($value)) {
			throw new \InvalidArgumentException(sprintf('Key "%s" must be an array or null', $key));
		}

		return $value;
	}

	/**
	 * @param array<mixed> $data
	 */
	public static function getBool(array $data, string $key): bool
	{
		$value = $data[$key] ?? null;

		if (!is_bool($value)) {
			throw new \InvalidArgumentException(sprintf('Key "%s" must be a boolean', $key));
		}

		return $value;
	}

	/**
	 * @param array<mixed> $data
	 */
	public static function getBoolOrNull(array $data, string $key): ?bool
	{
		$value = $data[$key] ?? null;

		if ($value === null) {
			return null;
		}

		if (!is_bool($value)) {
			throw new \InvalidArgumentException(sprintf('Key "%s" must be a boolean or null', $key));
		}

		return $value;
	}

	/**
	 * @param array<mixed> $data
	 */
	public static function getInt(array $data, string $key): int
	{
		$value = $data[$key] ?? null;

		if (!is_int($value)) {
			throw new \InvalidArgumentException(sprintf('Key "%s" must be an integer', $key));
		}

		return $value;
	}

	/**
	 * @param array<mixed> $data
	 */
	public static function getIntOrNull(array $data, string $key): ?int
	{
		$value = $data[$key] ?? null;

		if ($value === null) {
			return null;
		}

		if (!is_int($value)) {
			throw new \InvalidArgumentException(sprintf('Key "%s" must be an integer or null', $key));
		}

		return $value;
	}

	public static function merge(mixed $left, mixed $right): mixed
	{
		if (is_array($left) && is_array($right)) {
			reset($left);
			$firstKey = key($left);

			foreach ($left as $key => $val) {
				if ($firstKey === 0 && is_int($key)) {
					$right[] = $val;
				} else {
					if (isset($right[$key])) {
						$val = static::merge($val, $right[$key]);
					}

					$right[$key] = $val;
				}
			}

			return $right;
		}

		if ($left === null && is_array($right)) {
			return $right;
		}

		return $left;
	}

}
