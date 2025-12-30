<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class OAuthFlow
{

	private string $authorizationUrl;

	private string $tokenUrl;

	private string $refreshUrl;

	/** @var array<string, string> */
	private array $scopes = [];

	/**
	 * @param array<string, string> $scopes
	 */
	public function __construct(string $authorizationUrl, string $tokenUrl, string $refreshUrl, array $scopes)
	{
		$this->authorizationUrl = $authorizationUrl;
		$this->tokenUrl = $tokenUrl;
		$this->refreshUrl = $refreshUrl;
		$this->scopes = $scopes;
	}

	/**
	 * @param array{authorizationUrl: string, tokenUrl: string, refreshUrl: string, scopes: array<string, string>} $data
	 */
	public static function fromArray(array $data): self
	{
		/** @var string $authorizationUrl */
		$authorizationUrl = $data['authorizationUrl'];
		/** @var string $tokenUrl */
		$tokenUrl = $data['tokenUrl'];
		/** @var string $refreshUrl */
		$refreshUrl = $data['refreshUrl'];
		/** @var array<string, string> $scopes */
		$scopes = $data['scopes'];

		return new self(
			$authorizationUrl,
			$tokenUrl,
			$refreshUrl,
			$scopes,
		);
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		return [
			'authorizationUrl' => $this->authorizationUrl,
			'tokenUrl' => $this->tokenUrl,
			'refreshUrl' => $this->refreshUrl,
			'scopes' => $this->scopes,
		];
	}

	public function getAuthorizationUrl(): string
	{
		return $this->authorizationUrl;
	}

	public function setAuthorizationUrl(string $authorizationUrl): void
	{
		$this->authorizationUrl = $authorizationUrl;
	}

	public function getTokenUrl(): string
	{
		return $this->tokenUrl;
	}

	public function setTokenUrl(string $tokenUrl): void
	{
		$this->tokenUrl = $tokenUrl;
	}

	public function getRefreshUrl(): string
	{
		return $this->refreshUrl;
	}

	public function setRefreshUrl(string $refreshUrl): void
	{
		$this->refreshUrl = $refreshUrl;
	}

	/**
	 * @return array<string, string>
	 */
	public function getScopes(): array
	{
		return $this->scopes;
	}

	/**
	 * @param array<string, string> $scopes
	 */
	public function setScopes(array $scopes): void
	{
		$this->scopes = $scopes;
	}

}
