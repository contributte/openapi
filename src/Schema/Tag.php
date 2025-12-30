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
	 * @param array{name: string, description?: string, externalDocs?: array{url: string, description?: string}} $data
	 */
	public static function fromArray(array $data): Tag
	{
		/** @var string $name */
		$name = $data['name'];
		$tag = new Tag($name);
		/** @var string|null $description */
		$description = $data['description'] ?? null;
		$tag->setDescription($description);
		$externalDocs = $data['externalDocs'] ?? null;
		$tag->setExternalDocs($externalDocs !== null ? ExternalDocumentation::fromArray($externalDocs) : null);
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
