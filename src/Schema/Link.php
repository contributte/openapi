<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Link
{

	private ?string $operationRef = null;

	private ?string $operationId = null;

	/** @var mixed[] */
	private array $parameters = [];

	private mixed $requestBody = null;

	private ?string $description = null;

	private ?Server $server = null;

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param array{operationRef?: string, operationId?: string, parameters?: mixed[], requestBody?: mixed, description?: string, server?: array{url: string, description?: string}} $data
	 */
	public static function fromArray(array $data): Link
	{
		$link = new Link();
		/** @var string|null $operationRef */
		$operationRef = $data['operationRef'] ?? null;
		$link->setOperationRef($operationRef);
		$link->setOperationId($data['operationId'] ?? null);
		$link->setParameters($data['parameters'] ?? []);
		$link->setRequestBody($data['requestBody'] ?? null);
		$link->setDescription($data['description'] ?? null);
		/** @var array{url: string, description?: string, variables?: array<string, array{default: string, description?: string, enum?: string[]}>}|null $server */
		$server = $data['server'] ?? null;
		$link->setServer($server !== null ? Server::fromArray($server) : null);
		$link->setVendorExtensions(VendorExtensions::fromArray($data));

		return $link;
	}

	public function setOperationRef(?string $operationRef): void
	{
		$this->operationRef = $operationRef;
	}

	public function setOperationId(?string $operationId): void
	{
		$this->operationId = $operationId;
	}

	/**
	 * @param mixed[] $parameters
	 */
	public function setParameters(array $parameters): void
	{
		$this->parameters = $parameters;
	}

	public function setRequestBody(mixed $requestBody): void
	{
		$this->requestBody = $requestBody;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setServer(?Server $server): void
	{
		$this->server = $server;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		if ($this->operationRef !== null) {
			$data['operationRef'] = $this->operationRef;
		}

		if ($this->operationId !== null) {
			$data['operationId'] = $this->operationId;
		}

		if ($this->parameters !== []) {
			$data['parameters'] = $this->parameters;
		}

		if ($this->requestBody !== null) {
			$data['requestBody'] = $this->requestBody;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->server !== null) {
			$data['server'] = $this->server->toArray();
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
