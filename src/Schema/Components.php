<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Components
{

	/** @var Schema[]|Reference[] */
	private array $schemas = [];

	/** @var Response[]|Reference[] */
	private array $responses = [];

	/** @var Parameter[]|Reference[] */
	private array $parameters = [];

	/** @var Example[]|Reference[] */
	private array $examples = [];

	/** @var RequestBody[]|Reference[] */
	private array $requestBodies = [];

	/** @var Header[]|Reference[] */
	private array $headers = [];

	/** @var SecurityScheme[]|Reference[] */
	private array $securitySchemes = [];

	/** @var Link[]|Reference[] */
	private array $links = [];

	/** @var Callback[]|Reference[] */
	private array $callbacks = [];

	/** @var PathItem[]|Reference[] */
	private array $pathItems = [];

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param array{schemas?: array<string, mixed[]>, responses?: array<string, mixed[]>, parameters?: array<string, mixed[]>, examples?: array<string, mixed[]>, requestBodies?: array<string, mixed[]>, headers?: array<string, mixed[]>, securitySchemes?: array<string, mixed[]>, links?: array<string, mixed[]>, callbacks?: array<string, mixed[]>, pathItems?: array<string, mixed[]>} $data
	 */
	public static function fromArray(array $data): Components
	{
		$components = new Components();

		foreach ($data['schemas'] ?? [] as $schemaKey => $schemaData) {
			if (isset($schemaData['$ref'])) {
				$components->setSchema($schemaKey, Reference::fromArray($schemaData));
			} else {
				$components->setSchema($schemaKey, Schema::fromArray($schemaData));
			}
		}

		foreach ($data['responses'] ?? [] as $responseKey => $responseData) {
			if (isset($responseData['$ref'])) {
				$components->setResponse($responseKey, Reference::fromArray($responseData));
			} else {
				/** @var array{description: string, headers?: array<string, mixed[]>, content?: array<string, mixed[]>, links?: array<string, mixed[]>} $typedResponseData */
				$typedResponseData = $responseData;
				$components->setResponse($responseKey, Response::fromArray($typedResponseData));
			}
		}

		foreach ($data['parameters'] ?? [] as $parameterKey => $parameterData) {
			if (isset($parameterData['$ref'])) {
				$components->setParameter($parameterKey, Reference::fromArray($parameterData));
			} else {
				/** @var array{name: string, in: string, description?: string, required?: bool, deprecated?: bool, allowEmptyValue?: bool, style?: string, explode?: bool, allowReserved?: bool, schema?: mixed[], example?: mixed, examples?: mixed[]} $typedParameterData */
				$typedParameterData = $parameterData;
				$components->setParameter($parameterKey, Parameter::fromArray($typedParameterData));
			}
		}

		foreach ($data['examples'] ?? [] as $exampleKey => $exampleData) {
			if (isset($exampleData['$ref'])) {
				$components->setExample($exampleKey, Reference::fromArray($exampleData));
			} else {
				/** @var array{summary?: string, description?: string, value?: mixed, externalValue?: string} $typedExampleData */
				$typedExampleData = $exampleData;
				$components->setExample($exampleKey, Example::fromArray($typedExampleData));
			}
		}

		foreach ($data['requestBodies'] ?? [] as $requestBodyKey => $requestBodyData) {
			if (isset($requestBodyData['$ref'])) {
				$components->setRequestBody($requestBodyKey, Reference::fromArray($requestBodyData));
			} else {
				/** @var array{description?: string, required?: bool, content?: array<string, mixed[]>} $typedRequestBodyData */
				$typedRequestBodyData = $requestBodyData;
				$components->setRequestBody($requestBodyKey, RequestBody::fromArray($typedRequestBodyData));
			}
		}

		foreach ($data['headers'] ?? [] as $headerKey => $headerData) {
			if (isset($headerData['$ref'])) {
				$components->setHeader($headerKey, Reference::fromArray($headerData));
			} else {
				/** @var array{description?: string, required?: bool, deprecated?: bool, allowEmptyValue?: bool, style?: string, explode?: bool, allowReserved?: bool, schema?: mixed[], example?: mixed, examples?: mixed[]} $typedHeaderData */
				$typedHeaderData = $headerData;
				$components->setHeader($headerKey, Header::fromArray($typedHeaderData));
			}
		}

		foreach ($data['securitySchemes'] ?? [] as $securitySchemeKey => $securitySchemeData) {
			if (isset($securitySchemeData['$ref'])) {
				$components->setSecurityScheme($securitySchemeKey, Reference::fromArray($securitySchemeData));
			} else {
				/** @var array{type: string, name?: string, description?: string, in?: string, scheme?: string, bearerFormat?: string, flows?: array<string, mixed[]>, openIdConnectUrl?: string} $typedSecuritySchemeData */
				$typedSecuritySchemeData = $securitySchemeData;
				$components->setSecurityScheme($securitySchemeKey, SecurityScheme::fromArray($typedSecuritySchemeData));
			}
		}

		foreach ($data['callbacks'] ?? [] as $callbackKey => $callbackData) {
			if (isset($callbackData['$ref'])) {
				$components->setCallback($callbackKey, Reference::fromArray($callbackData));
			} else {
				$components->setCallback($callbackKey, Callback::fromArray($callbackData));
			}
		}

		foreach ($data['links'] ?? [] as $linkKey => $linkData) {
			if (isset($linkData['$ref'])) {
				$components->setLink($linkKey, Reference::fromArray($linkData));
			} else {
				/** @var array{operationRef?: string, operationId?: string, parameters?: mixed[], requestBody?: mixed, description?: string, server?: array{url: string, description?: string}} $typedLinkData */
				$typedLinkData = $linkData;
				$components->setLink($linkKey, Link::fromArray($typedLinkData));
			}
		}

		foreach ($data['pathItems'] ?? [] as $pathItemKey => $pathItemData) {
			if (isset($pathItemData['$ref'])) {
				$components->setPathItem($pathItemKey, Reference::fromArray($pathItemData));
			} else {
				/** @var array{summary?: string, description?: string, get?: mixed[], put?: mixed[], post?: mixed[], delete?: mixed[], options?: mixed[], head?: mixed[], patch?: mixed[], trace?: mixed[], servers?: mixed[], parameters?: mixed[]} $typedPathItemData */
				$typedPathItemData = $pathItemData;
				$components->setPathItem($pathItemKey, PathItem::fromArray($typedPathItemData));
			}
		}

		$components->setVendorExtensions(VendorExtensions::fromArray($data));

		return $components;
	}

	public function setSchema(string $name, Schema|Reference $schema): void
	{
		$this->schemas[$name] = $schema;
	}

	public function setResponse(string $name, Response|Reference $response): void
	{
		$this->responses[$name] = $response;
	}

	public function setParameter(string $name, Parameter|Reference $parameter): void
	{
		$this->parameters[$name] = $parameter;
	}

	public function setExample(string $name, Example|Reference $example): void
	{
		$this->examples[$name] = $example;
	}

	public function setRequestBody(string $name, RequestBody|Reference $requestBody): void
	{
		$this->requestBodies[$name] = $requestBody;
	}

	public function setHeader(string $name, Header|Reference $header): void
	{
		$this->headers[$name] = $header;
	}

	public function setSecurityScheme(string $name, SecurityScheme|Reference $securityScheme): void
	{
		$this->securitySchemes[$name] = $securityScheme;
	}

	public function setLink(string $name, Link|Reference $link): void
	{
		$this->links[$name] = $link;
	}

	public function setCallback(string $name, Callback|Reference $callback): void
	{
		$this->callbacks[$name] = $callback;
	}

	public function setPathItem(string $name, PathItem|Reference $pathItem): void
	{
		$this->pathItems[$name] = $pathItem;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		foreach ($this->schemas as $schemaKey => $schema) {
			$data['schemas'][$schemaKey] = $schema->toArray();
		}

		foreach ($this->responses as $responseKey => $response) {
			$data['responses'][$responseKey] = $response->toArray();
		}

		foreach ($this->parameters as $parameterKey => $parameter) {
			$data['parameters'][$parameterKey] = $parameter->toArray();
		}

		foreach ($this->examples as $exampleKey => $example) {
			$data['examples'][$exampleKey] = $example->toArray();
		}

		foreach ($this->requestBodies as $requestBodyKey => $requestBody) {
			$data['requestBodies'][$requestBodyKey] = $requestBody->toArray();
		}

		foreach ($this->headers as $headerKey => $header) {
			$data['headers'][$headerKey] = $header->toArray();
		}

		foreach ($this->securitySchemes as $securitySchemeKey => $securityScheme) {
			$data['securitySchemes'][$securitySchemeKey] = $securityScheme->toArray();
		}

		foreach ($this->links as $linkKey => $link) {
			$data['links'][$linkKey] = $link->toArray();
		}

		foreach ($this->callbacks as $callbackKey => $callback) {
			$data['callbacks'][$callbackKey] = $callback->toArray();
		}

		foreach ($this->pathItems as $pathItemKey => $pathItem) {
			$data['pathItems'][$pathItemKey] = $pathItem->toArray();
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
