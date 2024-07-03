<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Header
{

	private ?string $description = null;

	private ?bool $required = null;

	private ?bool $deprecated = null;

	private ?bool $allowEmptyValue = null;

	private ?string $style = null;

	private ?bool $explode = null;

	private ?bool $allowReserved = null;

	private Schema|Reference|null $schema = null;

	private mixed $example = null;

	/** @var mixed[] */
	private array $examples = [];

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Header
	{
		$header = new Header();
		$header->setDescription($data['description'] ?? null);
		$header->setRequired($data['required'] ?? null);
		$header->setDeprecated($data['deprecated'] ?? null);
		$header->setAllowEmptyValue($data['allowEmptyValue'] ?? null);
		$header->setStyle($data['style'] ?? null);
		$header->setExplode($data['explode'] ?? null);
		$header->setAllowReserved($data['allowReserved'] ?? null);

		if (isset($data['schema'])) {
			if (isset($data['schema']['$ref'])) {
				$header->setSchema(Reference::fromArray($data['schema']));
			} else {
				$header->setSchema(Schema::fromArray($data['schema']));
			}
		}

		$header->setExample($data['example'] ?? null);
		$header->setExamples($data['examples'] ?? []);

		return $header;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->required !== null) {
			$data['required'] = $this->required;
		}

		if ($this->deprecated !== null) {
			$data['deprecated'] = $this->deprecated;
		}

		if ($this->allowEmptyValue !== null) {
			$data['allowEmptyValue'] = $this->allowEmptyValue;
		}

		if ($this->style !== null) {
			$data['style'] = $this->style;
		}

		if ($this->explode !== null) {
			$data['explode'] = $this->explode;
		}

		if ($this->allowReserved !== null) {
			$data['allowReserved'] = $this->allowReserved;
		}

		if ($this->schema !== null) {
			$data['schema'] = $this->schema->toArray();
		}

		if ($this->example !== null) {
			$data['example'] = $this->example;
		}

		if ($this->examples !== []) {
			$data['examples'] = $this->examples;
		}

		return $data;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setRequired(?bool $required): void
	{
		$this->required = $required;
	}

	public function setDeprecated(?bool $deprecated): void
	{
		$this->deprecated = $deprecated;
	}

	public function setAllowEmptyValue(?bool $allowEmptyValue): void
	{
		$this->allowEmptyValue = $allowEmptyValue;
	}

	public function setStyle(?string $style): void
	{
		$this->style = $style;
	}

	public function setExplode(?bool $explode): void
	{
		$this->explode = $explode;
	}

	public function setAllowReserved(?bool $allowReserved): void
	{
		$this->allowReserved = $allowReserved;
	}

	public function setSchema(Schema|Reference|null $schema): void
	{
		$this->schema = $schema;
	}

	public function setExample(mixed $example): void
	{
		$this->example = $example;
	}

	/**
	 * @param mixed[] $examples
	 */
	public function setExamples(array $examples): void
	{
		$this->examples = $examples;
	}

}
