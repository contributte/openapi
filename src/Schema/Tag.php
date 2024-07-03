<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Tag
{

	private string $name;

	private ?string $description = null;

	private ?ExternalDocumentation $externalDocs = null;

	private ?VendorExtensions $vendorExtensions = null;

	public function __construct(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Tag
	{
		$tag = new Tag($data['name']);
		$tag->setDescription($data['description'] ?? null);
		$tag->setExternalDocs(isset($data['externalDocs']) ? ExternalDocumentation::fromArray($data['externalDocs']) : null);
		$tag->setVendorExtensions(VendorExtensions::fromArray($data));

		return $tag;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['name'] = $this->name;

		// Optional
		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->externalDocs !== null) {
			$data['externalDocs'] = $this->externalDocs->toArray();
		}

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setExternalDocs(?ExternalDocumentation $externalDocs): void
	{
		$this->externalDocs = $externalDocs;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getExternalDocs(): ?ExternalDocumentation
	{
		return $this->externalDocs;
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
