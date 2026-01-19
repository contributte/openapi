<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class OpenApi
{

	private string $openapi;

	private Info $info;

	private ?string $jsonSchemaDialect = null;

	/** @var Server[] */
	private array $servers = [];

	private ?Paths $paths;

	/** @var array<string, PathItem|Reference> */
	private array $webhooks = [];

	private ?Components $components = null;

	/** @var SecurityRequirement[] */
	private array $security = [];

	/** @var Tag[] */
	private array $tags = [];

	private ?ExternalDocumentation $externalDocs = null;

	private ?VendorExtensions $vendorExtensions = null;

	public function __construct(string $openapi, Info $info, ?Paths $paths = null)
	{
		$this->openapi = $openapi;
		$this->info = $info;
		$this->paths = $paths;
	}

	/**
	 * @param array{openapi: string, info: mixed[], jsonSchemaDialect?: string, servers?: array<mixed[]>, paths?: array<string, mixed[]>, webhooks?: array<string, mixed[]>, components?: mixed[], security?: array<array<string, array<string>>>, tags?: array<mixed[]>, externalDocs?: mixed[]} $data
	 */
	public static function fromArray(array $data): OpenApi
	{
		/** @var array{title: string, version: string, summary?: string, description?: string, termsOfService?: string, license?: mixed[], contact?: mixed[]} $info */
		$info = $data['info'];
		/** @var string $openapi */
		$openapi = $data['openapi'];
		$openApi = new OpenApi(
			$openapi,
			Info::fromArray($info),
		);

		/** @var string|null $jsonSchemaDialect */
		$jsonSchemaDialect = $data['jsonSchemaDialect'] ?? null;
		$openApi->jsonSchemaDialect = $jsonSchemaDialect;

		foreach ($data['servers'] ?? [] as $serverData) {
			/** @var array{url: string, description?: string, variables?: array<string, mixed[]>} $server */
			$server = $serverData;
			$openApi->addServer(Server::fromArray($server));
		}

		if (isset($data['paths'])) {
			$openApi->paths = Paths::fromArray($data['paths']);
		}

		/** @var array<string, mixed[]> $webhooks */
		$webhooks = $data['webhooks'] ?? [];
		foreach ($webhooks as $webhookId => $webhookData) {
			if (isset($webhookData['$ref'])) {
				$openApi->webhooks[$webhookId] = Reference::fromArray($webhookData);
			} else {
				/** @var array{get?: mixed[], put?: mixed[], post?: mixed[], delete?: mixed[], options?: mixed[], head?: mixed[], patch?: mixed[], trace?: mixed[], summary?: string, description?: string, servers?: array<mixed[]>, parameters?: array<mixed[]>} $webhook */
				$webhook = $webhookData;
				$openApi->webhooks[$webhookId] = PathItem::fromArray($webhook);
			}
		}

		if (isset($data['components'])) {
			/** @var array{schemas?: array<string, mixed[]>, responses?: array<string, mixed[]>, parameters?: array<string, mixed[]>, examples?: array<string, mixed[]>, requestBodies?: array<string, mixed[]>, headers?: array<string, mixed[]>, securitySchemes?: array<string, mixed[]>, links?: array<string, mixed[]>, callbacks?: array<string, mixed[]>, pathItems?: array<string, mixed[]>} $components */
			$components = $data['components'];
			$openApi->setComponents(Components::fromArray($components));
		}

		foreach ($data['tags'] ?? [] as $tagData) {
			/** @var array{name: string, description?: string, externalDocs?: array{url: string, description?: string}} $tag */
			$tag = $tagData;
			$openApi->addTag(Tag::fromArray($tag));
		}

		if (isset($data['externalDocs'])) {
			/** @var array{url: string, description?: string} $externalDocs */
			$externalDocs = $data['externalDocs'];
			$openApi->externalDocs = ExternalDocumentation::fromArray($externalDocs);
		}

		foreach ($data['security'] ?? [] as $securityItem) {
			/** @var array<string, array<string>> $security */
			$security = $securityItem;
			$openApi->addSecurityRequirement(SecurityRequirement::fromArray($security));
		}

		$openApi->setVendorExtensions(VendorExtensions::fromArray($data));

		return $openApi;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['openapi'] = $this->openapi;
		$data['info'] = $this->info->toArray();

		if ($this->jsonSchemaDialect !== null) {
			$data['jsonSchemaDialect'] = $this->jsonSchemaDialect;
		}

		foreach ($this->servers as $server) {
			$data['servers'][] = $server->toArray();
		}

		foreach ($this->webhooks as $webhookId => $webhook) {
			$data['webhooks'][$webhookId] = $webhook->toArray();
		}

		if ($this->paths !== null) {
			$data['paths'] = $this->paths->toArray();
		}

		if ($this->components !== null) {
			$data['components'] = $this->components->toArray();
		}

		foreach ($this->security as $requirement) {
			$data['security'][] = $requirement->toArray();
		}

		foreach ($this->tags as $tag) {
			$data['tags'][] = $tag->toArray();
		}

		if ($this->externalDocs !== null) {
			$data['externalDocs'] = $this->externalDocs->toArray();
		}

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

	public function addTag(Tag $tag): void
	{
		$this->tags[] = $tag;
	}

	public function addServer(Server $server): void
	{
		$this->servers[] = $server;
	}

	public function setComponents(?Components $components): void
	{
		$this->components = $components;
	}

	public function setExternalDocs(?ExternalDocumentation $externalDocs): void
	{
		$this->externalDocs = $externalDocs;
	}

	public function addSecurityRequirement(SecurityRequirement $security): void
	{
		$this->security[] = $security;
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
