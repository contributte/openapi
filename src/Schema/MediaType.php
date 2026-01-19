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
	 * @param array{schema?: mixed[], example?: mixed, examples?: array<string, mixed[]>, encoding?: array<string, mixed[]>} $data
	 */
	public static function fromArray(array $data): MediaType
	{
		$mediaType = new MediaType();

		if (isset($data['schema'])) {
			/** @var mixed[] $schema */
			$schema = $data['schema'];
			if (isset($schema['$ref'])) {
				$mediaType->setSchema(Reference::fromArray($schema));
			} else {
				$mediaType->setSchema(Schema::fromArray($schema));
			}
		}

		$mediaType->setExample($data['example'] ?? null);

		foreach ($data['examples'] ?? [] as $name => $example) {
			if (isset($example['$ref'])) {
				$mediaType->addExample($name, Reference::fromArray($example));
			} else {
				/** @var array{summary?: string, description?: string, value?: mixed, externalValue?: string} $exampleDataTyped */
				$exampleDataTyped = $example;
				$mediaType->addExample($name, Example::fromArray($exampleDataTyped));
			}
		}

		/** @var array<string, mixed[]> $encoding */
		$encoding = $data['encoding'] ?? [];
		foreach ($encoding as $name => $encodingItem) {
			if (isset($encodingItem['$ref'])) {
				$mediaType->addEncoding($name, Reference::fromArray($encodingItem));
			} else {
				/** @var array{contentType?: string, headers?: array<string, mixed[]>, style?: string, explode?: bool, allowReserved?: bool} $encodingData */
				$encodingData = $encodingItem;
				$mediaType->addEncoding($name, Encoding::fromArray($encodingData));
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
