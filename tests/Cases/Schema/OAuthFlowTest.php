<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\OAuthFlow;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class OAuthFlowTest extends TestCase
{

	public function testRequired(): void
	{
		$authorizationUrl = 'https://example.com/authorization';
		$tokenUrl = 'https://example.com/token';
		$refreshUrl = 'https://example.com/refresh';
		$scopes = ['read' => 'Read access', 'write' => 'Write access'];
		$flow = new OAuthFlow($authorizationUrl, $tokenUrl, $refreshUrl, $scopes);

		Assert::same($authorizationUrl, $flow->getAuthorizationUrl());
		Assert::same($tokenUrl, $flow->getTokenUrl());
		Assert::same($refreshUrl, $flow->getRefreshUrl());
		Assert::same($scopes, $flow->getScopes());

		$realData = $flow->toArray();
		$expectedData = [
			'authorizationUrl' => $authorizationUrl,
			'tokenUrl' => $tokenUrl,
			'refreshUrl' => $refreshUrl,
			'scopes' => $scopes,
		];

		Assert::same($expectedData, $realData);
		Assert::same($expectedData, OAuthFlow::fromArray($realData)->toArray());
	}

	public function testImplicitFlowWithoutTokenUrl(): void
	{
		$data = [
			'authorizationUrl' => 'https://example.com/authorization',
			'scopes' => ['read' => 'Read access'],
		];

		$flow = OAuthFlow::fromArray($data);

		Assert::same('https://example.com/authorization', $flow->getAuthorizationUrl());
		Assert::null($flow->getTokenUrl());
		Assert::null($flow->getRefreshUrl());
		Assert::same($data, $flow->toArray());
	}

	public function testClientCredentialsFlowWithoutAuthorizationUrl(): void
	{
		$data = [
			'tokenUrl' => 'https://example.com/token',
			'scopes' => [],
		];

		$flow = OAuthFlow::fromArray($data);

		Assert::null($flow->getAuthorizationUrl());
		Assert::same('https://example.com/token', $flow->getTokenUrl());
		Assert::same($data, $flow->toArray());
	}

	public function testVendorExtensions(): void
	{
		$expectedData = [
			'authorizationUrl' => 'https://example.com/authorization',
			'scopes' => ['read' => 'Read access'],
			'x-internal' => true,
		];

		$flow = OAuthFlow::fromArray($expectedData);

		Assert::same(true, $flow->getVendorExtensions()?->getExtension('x-internal'));
		Assert::same($expectedData, $flow->toArray());
	}

	/**
	 * The OpenAPI Specification marks `scopes` as REQUIRED on the OAuth Flow Object (the map MAY
	 * be empty, but the key MUST be present). fromArray() must not silently manufacture it - a
	 * missing key has to surface as an error instead of producing an OAuthFlow with scopes = [].
	 */
	public function testMissingScopesIsNotSilentlyAccepted(): void
	{
		$data = [
			'tokenUrl' => 'https://example.com/token',
		];

		Assert::exception(static function () use ($data): void {
			Assert::error(static function () use ($data): void {
				OAuthFlow::fromArray($data);
			}, E_WARNING, 'Undefined array key "scopes"');
		}, \TypeError::class);
	}

}

(new OAuthFlowTest())->run();
