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

		/** @var bool|null $deprecated */
		$deprecated = $data['deprecated'] ?? null;
		if ($deprecated !== null) {
			$operation->setDeprecated($deprecated);
		}

		/** @var string|null $operationId */
		$operationId = $data['operationId'] ?? null;
		$operation->setOperationId($operationId);
		/** @var string[] $tags */
		$tags = $data['tags'] ?? [];
		$operation->setTags($tags);
		/** @var string|null $summary */
		$summary = $data['summary'] ?? null;
		$operation->setSummary($summary);
		/** @var string|null $description */
		$description = $data['description'] ?? null;
		$operation->setDescription($description);

		/** @var mixed[]|null $externalDocs */
		$externalDocs = $data['externalDocs'] ?? null;
		if ($externalDocs !== null) {
			$operation->setExternalDocs(ExternalDocumentation::fromArray($externalDocs));
		}

		/** @var mixed[] $parameters */
		$parameters = $data['parameters'] ?? [];
		foreach ($parameters as $parameterData) {
			/** @var mixed[] $parameterData */
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

		/** @var mixed[]|null $requestBody */
		$requestBody = $data['requestBody'] ?? null;
		if ($requestBody !== null) {
			if (isset($requestBody['$ref'])) {
				$operation->setRequestBody(Reference::fromArray($requestBody));
			} else {
				$operation->setRequestBody(RequestBody::fromArray($requestBody));
			}
		}

		/** @var mixed[]|null $responses */
		$responses = $data['responses'] ?? null;
		if ($responses !== null) {
			$operation->setResponses(Responses::fromArray($responses));
		}

		/** @var mixed[]|null $security */
		$security = $data['security'] ?? null;
		if ($security !== null && $security === []) {
			$operation->setEmptySecurityRequirement();
		}

		foreach ($security ?? [] as $securityRequirementData) {
			/** @var mixed[] $securityRequirementData */
			$operation->addSecurityRequirement(SecurityRequirement::fromArray($securityRequirementData));
		}

		/** @var mixed[] $servers */
		$servers = $data['servers'] ?? [];
		foreach ($servers as $server) {
			/** @var mixed[] $server */
			$operation->addServer(Server::fromArray($server));
		}

		/** @var array<string, mixed[]> $callbacks */
		$callbacks = $data['callbacks'] ?? [];
		foreach ($callbacks as $expression => $callback) {
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
		/** @var array<mixed> $mergedArray */
		$mergedArray = is_array($merged) ? $merged : [];
		$parameter = Parameter::fromArray($mergedArray);

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
