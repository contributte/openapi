<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class VendorExtensions
{

	/** @param array<string, mixed> $extensions */
	public function __construct(
		private array $extensions = [],
	)
	{
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): ?VendorExtensions
	{
		$extensions = [];

		foreach ($data as $name => $value) {
			if (str_starts_with((string) $name, 'x-')) {
				$extensions[$name] = $value;
			}
		}

		if ($extensions === []) {
			return null;
		}

		return new VendorExtensions($extensions);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function toArray(): array
	{
		return $this->extensions;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getExtensions(): array
	{
		return $this->extensions;
	}

	public function getExtension(string $name): mixed
	{
		return $this->extensions[$name] ?? null;
	}

	public function setExtension(string $name, mixed $value): void
	{
		$this->extensions[$name] = $value;
	}

}
