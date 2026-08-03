<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class OAuthFlow
{

	private ?string $authorizationUrl;

	private ?string $tokenUrl;

	private ?string $refreshUrl;

	/** @var array<string, string> */
	private array $scopes;

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param array<string, string> $scopes
	 */
	public function __construct(
		?string $authorizationUrl = null,
		?string $tokenUrl = null,
		?string $refreshUrl = null,
		array $scopes = [],
	)
	{
		$this->authorizationUrl = $authorizationUrl;
		$this->tokenUrl = $tokenUrl;
		$this->refreshUrl = $refreshUrl;
		$this->scopes = $scopes;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): self
	{
		$flow = new self(
			$data['authorizationUrl'] ?? null,
			$data['tokenUrl'] ?? null,
			$data['refreshUrl'] ?? null,
			$data['scopes'],
		);
		$flow->setVendorExtensions(VendorExtensions::fromArray($data));

		return $flow;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		if ($this->authorizationUrl !== null) {
			$data['authorizationUrl'] = $this->authorizationUrl;
		}

		if ($this->tokenUrl !== null) {
			$data['tokenUrl'] = $this->tokenUrl;
		}

		if ($this->refreshUrl !== null) {
			$data['refreshUrl'] = $this->refreshUrl;
		}

		$data['scopes'] = $this->scopes;

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

	public function getAuthorizationUrl(): ?string
	{
		return $this->authorizationUrl;
	}

	public function setAuthorizationUrl(?string $authorizationUrl): void
	{
		$this->authorizationUrl = $authorizationUrl;
	}

	public function getTokenUrl(): ?string
	{
		return $this->tokenUrl;
	}

	public function setTokenUrl(?string $tokenUrl): void
	{
		$this->tokenUrl = $tokenUrl;
	}

	public function getRefreshUrl(): ?string
	{
		return $this->refreshUrl;
	}

	public function setRefreshUrl(?string $refreshUrl): void
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

	public function getVendorExtensions(): ?VendorExtensions
	{
		return $this->vendorExtensions;
	}

	public function setVendorExtensions(?VendorExtensions $vendorExtensions): void
	{
		$this->vendorExtensions = $vendorExtensions;
	}

}
