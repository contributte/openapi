<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class License
{

	private string $name;

	private ?string $identifier = null;

	private ?string $url = null;

	private ?VendorExtensions $vendorExtensions = null;

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): License
	{
		$license = new License($data['name']);
		$license->setIdentifier($data['identifier'] ?? null);
		$license->setUrl($data['url'] ?? null);
		$license->setVendorExtensions(VendorExtensions::fromArray($data));

		return $license;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['name'] = $this->name;

		// Optional SPDX identifier
		if ($this->identifier !== null) {
			$data['identifier'] = $this->identifier;
		}

		// Optional url
		if ($this->url !== null) {
			$data['url'] = $this->url;
		}

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

	public function setIdentifier(?string $identifier): void
	{
		$this->identifier = $identifier;
	}

	public function setUrl(?string $url): void
	{
		$this->url = $url;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getIdentifier(): ?string
	{
		return $this->identifier;
	}

	public function getUrl(): ?string
	{
		return $this->url;
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
