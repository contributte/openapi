<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class ServerVariable
{

	/** @var string[] */
	private array $enum = [];

	private string $default;

	private ?string $description = null;

	private ?VendorExtensions $vendorExtensions = null;

	public function __construct(string $default)
	{
		$this->default = $default;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): ServerVariable
	{
		$variable = new ServerVariable($data['default']);
		$variable->setDescription($data['description'] ?? null);
		$variable->setEnum($data['enum'] ?? []);
		$variable->setVendorExtensions(VendorExtensions::fromArray($data));

		return $variable;
	}

	/**
	 * @param string[] $enum
	 */
	public function setEnum(array $enum): void
	{
		$this->enum = $enum;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	/**
	 * @return string[]
	 */
	public function getEnum(): array
	{
		return $this->enum;
	}

	public function getDefault(): string
	{
		return $this->default;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		if ($this->enum !== []) {
			$data['enum'] = $this->enum;
		}

		$data['default'] = $this->default;

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
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
