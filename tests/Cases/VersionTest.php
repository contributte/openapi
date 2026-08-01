<?php declare(strict_types = 1);

namespace Tests\Cases;

use Contributte\OpenApi\Version;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class VersionTest extends TestCase
{

	public function testMatchExactVersion(): void
	{
		Assert::true(Version::match('3.0.4', '3.0.4'));
		Assert::false(Version::match('3.0.4', '3.0.3'));
	}

	public function testMatchWildcard(): void
	{
		Assert::true(Version::match('3.0.4', '3.0.x'));
		Assert::true(Version::match('3.0', '3.0.x'));
		Assert::true(Version::match('3.1.1', '3.x'));
		Assert::false(Version::match('3.1.1', '3.0.x'));
		Assert::false(Version::match('2.0', '3.x'));
	}

	public function testMatchIgnoresTrailingSegments(): void
	{
		Assert::true(Version::match('3.1.1', '3.1'));
		Assert::false(Version::match('3.1.1', '3.2'));
	}

	public function testIsBefore(): void
	{
		Assert::true(Version::isBefore('3.0.4', '3.1'));
		Assert::false(Version::isBefore('3.1', '3.1'));
		Assert::false(Version::isBefore('3.1.1', '3.1'));
		Assert::false(Version::isBefore('3.2.0', '3.1'));
	}

	public function testIsSupported(): void
	{
		Assert::true(Version::isSupported('3.0.4'));
		Assert::true(Version::isSupported('3.1.1'));
		Assert::false(Version::isSupported('3.2.0'));
		Assert::false(Version::isSupported('2.0'));
		Assert::false(Version::isSupported('nonsense'));
	}

}

(new VersionTest())->run();
