<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

use InvalidArgumentException;

class Parameter
{

	public const IN_COOKIE = 'cookie';
	public const IN_HEADER = 'header';
	public const IN_PATH = 'path';
	public const IN_QUERY = 'query';

	public const INS = [
		self::IN_COOKIE,
		self::IN_HEADER,
		self::IN_PATH,
		self::IN_QUERY,
	];

	private string $name;

	private string $in;

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

	private ?VendorExtensions $vendorExtensions = null;

	public function __construct(string $name, string $in)
	{
		if (!in_array($in, self::INS, true)) {
			throw new InvalidArgumentException(sprintf(
				'Invalid value "%s" for attribute "in" given. It must be one of "%s".',
				$in,
				implode(', ', self::INS)
			));
		}

		$this->name = $name;
		$this->in = $in;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Parameter
	{
		$parameter = new Parameter($data['name'], $data['in']);
		$parameter->setDescription($data['description'] ?? null);
		$parameter->setRequired($data['required'] ?? null);
		$parameter->setDeprecated($data['deprecated'] ?? null);
		$parameter->setAllowEmptyValue($data['allowEmptyValue'] ?? null);
		$parameter->setStyle($data['style'] ?? null);
		$parameter->setExplode($data['explode'] ?? null);
		$parameter->setAllowReserved($data['allowReserved'] ?? null);

		if (isset($data['schema'])) {
			if (isset($data['schema']['$ref'])) {
				$parameter->setSchema(Reference::fromArray($data['schema']));
			} else {
				$parameter->setSchema(Schema::fromArray($data['schema']));
			}
		}

		$parameter->setExample($data['example'] ?? null);
		$parameter->setExamples($data['examples'] ?? []);
		$parameter->setVendorExtensions(VendorExtensions::fromArray($data));

		return $parameter;
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

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['name'] = $this->name;
		$data['in'] = $this->in;

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

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getIn(): string
	{
		return $this->in;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function isRequired(): ?bool
	{
		return $this->required;
	}

	public function isDeprecated(): ?bool
	{
		return $this->deprecated;
	}

	public function isAllowEmptyValue(): ?bool
	{
		return $this->allowEmptyValue;
	}

	public function getSchema(): Reference|Schema|null
	{
		return $this->schema;
	}

	public function getExample(): mixed
	{
		return $this->example;
	}

	public function getStyle(): ?string
	{
		return $this->style;
	}

	public function setStyle(?string $style): void
	{
		$this->style = $style;
	}

	public function isExplode(): ?bool
	{
		return $this->explode;
	}

	public function setExplode(?bool $explode): void
	{
		$this->explode = $explode;
	}

	public function isAllowReserved(): ?bool
	{
		return $this->allowReserved;
	}

	public function setAllowReserved(?bool $allowReserved): void
	{
		$this->allowReserved = $allowReserved;
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
