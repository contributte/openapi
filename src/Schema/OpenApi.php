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
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): OpenApi
	{
		/** @var array{title: string, version: string, summary?: string, description?: string, termsOfService?: string, contact?: array{name?: string, url?: string, email?: string}, license?: array{name: string, identifier?: string, url?: string}} $info */
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
			if (!is_array($serverData)) {
				continue;
			}

			/** @var array{url: string, description?: string, variables?: array<string, array{default: string, enum?: array<string>, description?: string}>} $server */
			$server = $serverData;
			$openApi->addServer(Server::fromArray($server));
		}

		if (isset($data['paths'])) {
			$openApi->paths = Paths::fromArray($data['paths']);
		}

		/** @var array<string, mixed[]> $webhooks */
		$webhooks = $data['webhooks'] ?? [];
		foreach ($webhooks as $webhookId => $webhookData) {
			$webhook = isset($webhookData['$ref']) ? Reference::fromArray($webhookData) : PathItem::fromArray($webhookData);
			$openApi->webhooks[$webhookId] = $webhook;
		}

		if (isset($data['components'])) {
			/** @var mixed[] $components */
			$components = $data['components'];
			$openApi->setComponents(Components::fromArray($components));
		}

		foreach ($data['tags'] ?? [] as $tagData) {
			if (!is_array($tagData)) {
				continue;
			}

			/** @var array{name: string, description?: string, externalDocs?: array{description?: string, url: string}} $tag */
			$tag = $tagData;
			$openApi->addTag(Tag::fromArray($tag));
		}

		if (isset($data['externalDocs'])) {
			/** @var array{description?: string, url: string} $externalDocs */
			$externalDocs = $data['externalDocs'];
			$openApi->externalDocs = ExternalDocumentation::fromArray($externalDocs);
		}

		foreach ($data['security'] ?? [] as $securityItem) {
			if (!is_array($securityItem)) {
				continue;
			}

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
