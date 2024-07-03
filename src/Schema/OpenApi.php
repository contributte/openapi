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
		$openApi = new OpenApi(
			$data['openapi'],
			Info::fromArray($data['info']),
		);

		if (isset($data['jsonSchemaDialect'])) {
			$openApi->jsonSchemaDialect = $data['jsonSchemaDialect'];
		}

		foreach ($data['servers'] ?? [] as $serverData) {
			$openApi->addServer(Server::fromArray($serverData));
		}

		if (isset($data['paths'])) {
			$openApi->paths = Paths::fromArray($data['paths']);
		}

		foreach ($data['webhooks'] ?? [] as $webhookId => $webhookData) {
			$webhook = isset($webhookData['$ref']) ? Reference::fromArray($webhookData) : PathItem::fromArray($webhookData);
			$openApi->webhooks[(string) $webhookId] = $webhook;
		}

		if (isset($data['components'])) {
			$openApi->setComponents(Components::fromArray($data['components']));
		}

		foreach ($data['tags'] ?? [] as $tagData) {
			$openApi->addTag(Tag::fromArray($tagData));
		}

		if (isset($data['externalDocs'])) {
			$openApi->externalDocs = ExternalDocumentation::fromArray($data['externalDocs']);
		}

		foreach ($data['security'] ?? [] as $security) {
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
