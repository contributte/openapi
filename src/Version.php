<?php declare(strict_types = 1);

namespace Contributte\OpenApi;

class Version
{

	public const V3_0 = '3.0';
	public const V3_1 = '3.1';

	public const SUPPORTED = [
		self::V3_0,
		self::V3_1,
	];

	/**
	 * Compares a version against a pattern. A pattern segment of "x" or "*" matches
	 * anything, and segments the pattern does not mention are ignored, so both
	 * "3.0.4" and "3.0" match the pattern "3.0.x".
	 */
	public static function match(string $version, string $pattern): bool
	{
		$versionSegments = explode('.', $version);

		foreach (explode('.', $pattern) as $index => $patternSegment) {
			if ($patternSegment === 'x' || $patternSegment === '*') {
				continue;
			}

			if (($versionSegments[$index] ?? null) !== $patternSegment) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Whether the version precedes the other one. Missing segments count as zero,
	 * so "3.0.4" precedes "3.1" and "3.1.1" does not.
	 */
	public static function isBefore(string $version, string $other): bool
	{
		return version_compare($version, $other, '<');
	}

	public static function isSupported(string $version): bool
	{
		foreach (self::SUPPORTED as $supported) {
			if (self::match($version, $supported)) {
				return true;
			}
		}

		return false;
	}

}
