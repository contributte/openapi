<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class MediaType
{

	private Schema|Reference|null $schema = null;

	private mixed $example = null;

	/** @var string[]|Example[]|Reference[] */
	private array $examples = [];

	/** @var array<string, Encoding|Reference> */
	private array $encoding = [];

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): MediaType
	{
		$mediaType = new MediaType();

		if (isset($data['schema'])) {
			if (isset($data['schema']['$ref'])) {
				$mediaType->setSchema(Reference::fromArray($data['schema']));
			} else {
				$mediaType->setSchema(Schema::fromArray($data['schema']));
			}
		}

		$mediaType->setExample($data['example'] ?? null);

		if (isset($data['examples'])) {
			foreach ($data['examples'] as $name => $example) {
				if (isset($example['$ref'])) {
					$mediaType->addExample($name, Reference::fromArray($example));
				} else {
					$mediaType->addExample($name, Example::fromArray($example));
				}
			}
		}

		foreach ($data['encoding'] ?? [] as $name => $encoding) {
			if (isset($encoding['$ref'])) {
				$mediaType->addEncoding($name, Reference::fromArray($encoding));
			} else {
				$mediaType->addEncoding($name, Encoding::fromArray($encoding));
			}
		}

		$mediaType->setVendorExtensions(VendorExtensions::fromArray($data));

		return $mediaType;
	}

	public function getSchema(): Schema|Reference|null
	{
		return $this->schema;
	}

	public function setSchema(Schema|Reference|null $schema): void
	{
		$this->schema = $schema;
	}

	public function getExample(): mixed
	{
		return $this->example;
	}

	public function setExample(mixed $example): void
	{
		$this->example = $example;
	}

	public function addExample(string $name, Example|Reference|string $example): void
	{
		$this->examples[$name] = $example;
	}

	public function addEncoding(string $name, Encoding|Reference $encoding): void
	{
		$this->encoding[$name] = $encoding;
	}

	public function getVendorExtensions(): ?VendorExtensions
	{
		return $this->vendorExtensions;
	}

	public function setVendorExtensions(?VendorExtensions $vendorExtensions): void
	{
		$this->vendorExtensions = $vendorExtensions;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		if ($this->schema !== null) {
			$data['schema'] = $this->schema->toArray();
		}

		if ($this->example !== null) {
			$data['example'] = $this->example;
		}

		if ($this->examples !== []) {
			$data['examples'] = array_map(static fn ($example) => is_string($example) ? $example : $example->toArray(), $this->examples);
		}

		if ($this->encoding !== []) {
			$data['encoding'] = array_map(static fn (Encoding|Reference $encoding) => $encoding->toArray(), $this->encoding);
		}

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

}
