<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class PathItem
{

	public const OPERATION_GET = 'get';
	public const OPERATION_PUT = 'put';
	public const OPERATION_POST = 'post';
	public const OPERATION_DELETE = 'delete';
	public const OPERATION_OPTIONS = 'options';
	public const OPERATION_HEAD = 'head';
	public const OPERATION_PATCH = 'patch';
	public const OPERATION_TRACE = 'trace';

	/** @var string[] */
	private static array $allowedOperations = [
		self::OPERATION_GET,
		self::OPERATION_PUT,
		self::OPERATION_POST,
		self::OPERATION_DELETE,
		self::OPERATION_OPTIONS,
		self::OPERATION_HEAD,
		self::OPERATION_PATCH,
		self::OPERATION_TRACE,
	];

	private ?string $summary = null;

	private ?string $description = null;

	/** @var Operation[] */
	private array $operations = [];

	/** @var Server[] */
	private array $servers = [];

	/** @var Parameter[]|Reference[] */
	private array $params = [];

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param array{get?: mixed[], put?: mixed[], post?: mixed[], delete?: mixed[], options?: mixed[], head?: mixed[], patch?: mixed[], trace?: mixed[], summary?: string, description?: string, servers?: array<mixed[]>, parameters?: array<mixed[]>} $pathItemData
	 */
	public static function fromArray(array $pathItemData): PathItem
	{
		$pathItem = new PathItem();

		foreach (self::$allowedOperations as $allowedOperation) {
			if (!isset($pathItemData[$allowedOperation])) {
				continue;
			}

			/** @var array{tags?: array<string>, summary?: string, description?: string, externalDocs?: mixed[], operationId?: string, parameters?: array<mixed[]>, requestBody?: mixed[], responses?: array<string, mixed[]>, callbacks?: array<string, mixed[]>, deprecated?: bool, security?: array<array<string, array<string>>>, servers?: array<mixed[]>} $operationData */
			$operationData = $pathItemData[$allowedOperation];
			$pathItem->setOperation($allowedOperation, Operation::fromArray($operationData));
		}

		$pathItem->setSummary($pathItemData['summary'] ?? null);
		$pathItem->setDescription($pathItemData['description'] ?? null);

		foreach ($pathItemData['servers'] ?? [] as $server) {
			/** @var array{url: string, description?: string, variables?: array<string, mixed[]>} $serverData */
			$serverData = $server;
			$pathItem->addServer(Server::fromArray($serverData));
		}

		foreach ($pathItemData['parameters'] ?? [] as $parameter) {
			if (isset($parameter['$ref'])) {
				$pathItem->addParameter(Reference::fromArray($parameter));
			} else {
				/** @var array{name: string, in: string, description?: string, required?: bool, deprecated?: bool, allowEmptyValue?: bool, style?: string, explode?: bool, allowReserved?: bool, schema?: mixed[], example?: mixed, examples?: mixed[]} $param */
				$param = $parameter;
				$pathItem->addParameter(Parameter::fromArray($param));
			}
		}

		$pathItem->setVendorExtensions(VendorExtensions::fromArray($pathItemData));

		return $pathItem;
	}

	public function addParameter(Parameter|Reference $parameter): void
	{
		$this->params[] = $parameter;
	}

	public function addServer(Server $server): void
	{
		$this->servers[] = $server;
	}

	public function setSummary(?string $summary): void
	{
		$this->summary = $summary;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setOperation(string $key, Operation $operation): void
	{
		if (!in_array($key, self::$allowedOperations, true)) {
			return;
		}

		$this->operations[$key] = $operation;
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
	 * @return Parameter[]|Reference[]
	 */
	public function getParameters(): array
	{
		return $this->params;
	}

	/**
	 * @return Server[]
	 */
	public function getServers(): array
	{
		return $this->servers;
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

		foreach ($this->operations as $key => $operation) {
			$data[$key] = $operation->toArray();
		}

		if ($this->summary !== null) {
			$data['summary'] = $this->summary;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->servers !== []) {
			$data['servers'] = array_map(static fn (Server $server): array => $server->toArray(), $this->servers);
		}

		if ($this->params !== []) {
			$data['parameters'] = array_map(static fn (Parameter|Reference $parameter): array => $parameter->toArray(), $this->params);
		}

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

}
