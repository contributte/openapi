<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Reference
{

	private string $ref;

	private ?string $summary = null;

	private ?string $description = null;

	public function __construct(string $ref)
	{
		$this->ref = $ref;
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public static function fromArray(array $data): Reference
	{
		/** @var string $ref */
		$ref = $data['$ref'];
		$reference = new Reference($ref);
		/** @var string|null $summary */
		$summary = $data['summary'] ?? null;
		$reference->setSummary($summary);
		/** @var string|null $description */
		$description = $data['description'] ?? null;
		$reference->setDescription($description);

		return $reference;
	}

	public function setSummary(?string $summary): void
	{
		$this->summary = $summary;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function getRef(): string
	{
		return $this->ref;
	}

	public function getSummary(): ?string
	{
		return $this->summary;
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
		$data = [
			'$ref' => $this->ref,
		];

		if ($this->summary !== null) {
			$data['summary'] = $this->summary;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		return $data;
	}

}
