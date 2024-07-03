<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

use Contributte\OpenApi\Utils\Helpers;

class Operation
{

	/** @var string[] */
	private array $tags = [];

	private ?string $summary = null;

	private ?string $description = null;

	private ?ExternalDocumentation $externalDocs = null;

	private ?string $operationId = null;

	/** @var Parameter[]|Reference[] */
	private array $parameters = [];

	private RequestBody|Reference|null $requestBody = null;

	private ?Responses $responses;

	/** @var Callback[]|Reference[] */
	private array $callbacks = [];

	private bool $deprecated = false;

	/** @var SecurityRequirement[]|null */
	private ?array $security = null;

	/** @var Server[] */
	private array $servers = [];

	private ?VendorExtensions $vendorExtensions = null;

	public function __construct(?Responses $responses = null)
	{
		$this->responses = $responses;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Operation
	{
		$operation = new Operation();

		if (isset($data['deprecated'])) {
			$operation->setDeprecated($data['deprecated']);
		}

		$operation->setOperationId($data['operationId'] ?? null);
		$operation->setTags($data['tags'] ?? []);
		$operation->setSummary($data['summary'] ?? null);
		$operation->setDescription($data['description'] ?? null);

		if (isset($data['externalDocs'])) {
			$operation->setExternalDocs(ExternalDocumentation::fromArray($data['externalDocs']));
		}

		foreach ($data['parameters'] ?? [] as $parameterData) {
			if (isset($parameterData['$ref'])) {
				$operation->addParameter(Reference::fromArray($parameterData));

				continue;
			}

			$parameter = Parameter::fromArray($parameterData);

			if ($operation->hasParameter($parameter)) {
				$operation->mergeParameter($parameter);
			} else {
				$operation->addParameter(Parameter::fromArray($parameterData));
			}
		}

		if (isset($data['requestBody'])) {
			if (isset($data['requestBody']['$ref'])) {
				$operation->setRequestBody(Reference::fromArray($data['requestBody']));
			} else {
				$operation->setRequestBody(RequestBody::fromArray($data['requestBody']));
			}
		}

		if (isset($data['responses'])) {
			$operation->setResponses(Responses::fromArray($data['responses']));
		}

		if (isset($data['security']) && $data['security'] === []) {
			$operation->setEmptySecurityRequirement();
		}

		foreach ($data['security'] ?? [] as $securityRequirementData) {
			$operation->addSecurityRequirement(SecurityRequirement::fromArray($securityRequirementData));
		}

		foreach ($data['servers'] ?? [] as $server) {
			$operation->addServer(Server::fromArray($server));
		}

		foreach ($data['callbacks'] ?? [] as $expression => $callback) {
			if (isset($callback['$ref'])) {
				$operation->addCallback($expression, Reference::fromArray($callback));
			} else {
				$operation->addCallback($expression, Callback::fromArray($callback));
			}
		}

		$operation->setVendorExtensions(VendorExtensions::fromArray($data));

		return $operation;
	}

	public function setOperationId(?string $operationId): void
	{
		$this->operationId = $operationId;
	}

	/**
	 * @param string[] $tags
	 */
	public function setTags(array $tags): void
	{
		$this->tags = $tags;
	}

	public function setSummary(?string $summary): void
	{
		$this->summary = $summary;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setExternalDocs(?ExternalDocumentation $externalDocs): void
	{
		$this->externalDocs = $externalDocs;
	}

	public function addParameter(Parameter|Reference $parameter): void
	{
		if ($parameter instanceof Parameter) {
			$this->parameters[$this->getParameterKey($parameter)] = $parameter;

			return;
		}

		$this->parameters[] = $parameter;
	}

	public function hasParameter(Parameter $parameter): bool
	{
		return array_key_exists($this->getParameterKey($parameter), $this->parameters);
	}

	public function mergeParameter(Parameter $parameter): void
	{
		$originalParameter = $this->parameters[$this->getParameterKey($parameter)];

		$merged = Helpers::merge($parameter->toArray(), $originalParameter->toArray());
		$parameter = Parameter::fromArray($merged);

		$this->parameters[$this->getParameterKey($parameter)] = $parameter;
	}

	public function setRequestBody(RequestBody|Reference|null $requestBody): void
	{
		$this->requestBody = $requestBody;
	}

	public function setResponses(?Responses $responses): void
	{
		$this->responses = $responses;
	}

	public function addCallback(string $expression, Callback|Reference $callback): void
	{
		$this->callbacks[$expression] = $callback;
	}

	public function setDeprecated(bool $deprecated): void
	{
		$this->deprecated = $deprecated;
	}

	public function setEmptySecurityRequirement(): void
	{
		$this->security = [];
	}

	public function addSecurityRequirement(SecurityRequirement $securityRequirement): void
	{
		if ($this->security === null) {
			$this->security = [];
		}

		$this->security[] = $securityRequirement;
	}

	public function addServer(Server $server): void
	{
		$this->servers[] = $server;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		if ($this->deprecated) {
			$data['deprecated'] = $this->deprecated;
		}

		if ($this->tags !== []) {
			$data['tags'] = $this->tags;
		}

		if ($this->summary !== null) {
			$data['summary'] = $this->summary;
		}

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->externalDocs !== null) {
			$data['externalDocs'] = $this->externalDocs->toArray();
		}

		if ($this->operationId !== null) {
			$data['operationId'] = $this->operationId;
		}

		foreach ($this->parameters as $parameter) {
			$data['parameters'][] = $parameter->toArray();
		}

		if ($this->requestBody !== null) {
			$data['requestBody'] = $this->requestBody->toArray();
		}

		if ($this->security !== null) {
			$data['security'] = [];

			foreach ($this->security as $securityRequirement) {
				$data['security'][] = $securityRequirement->toArray();
			}
		}

		if ($this->responses !== null) {
			$data['responses'] = $this->responses->toArray();
		}

		foreach ($this->servers as $server) {
			$data['servers'][] = $server->toArray();
		}

		foreach ($this->callbacks as $expression => $callback) {
			$data['callbacks'][$expression] = $callback->toArray();
		}

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

	/**
	 * @return string[]
	 */
	public function getTags(): array
	{
		return $this->tags;
	}

	public function getSummary(): ?string
	{
		return $this->summary;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getExternalDocs(): ?ExternalDocumentation
	{
		return $this->externalDocs;
	}

	public function getOperationId(): ?string
	{
		return $this->operationId;
	}

	/**
	 * @return Parameter[]|Reference[]
	 */
	public function getParameters(): array
	{
		return $this->parameters;
	}

	public function getRequestBody(): RequestBody|Reference|null
	{
		return $this->requestBody;
	}

	public function getResponses(): ?Responses
	{
		return $this->responses;
	}

	/**
	 * @return Reference[]|Callback[]
	 */
	public function getCallbacks(): array
	{
		return $this->callbacks;
	}

	public function isDeprecated(): bool
	{
		return $this->deprecated;
	}

	/**
	 * @return SecurityRequirement[]|null
	 */
	public function getSecurity(): ?array
	{
		return $this->security;
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

	private function getParameterKey(Parameter $parameter): string
	{
		return $parameter->getIn() . '-' . $parameter->getName();
	}

}
