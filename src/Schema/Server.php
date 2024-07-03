<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Server
{

	private string $url;

	private ?string $description = null;

	/** @var ServerVariable[] */
	private array $variables = [];

	private ?VendorExtensions $vendorExtensions = null;

	public function __construct(string $url)
	{
		$this->url = $url;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Server
	{
		$server = new Server($data['url']);
		$server->setDescription($data['description'] ?? null);

		foreach ($data['variables'] ?? [] as $key => $variable) {
			$server->addVariable($key, ServerVariable::fromArray($variable));
		}

		$server->setVendorExtensions(VendorExtensions::fromArray($data));

		return $server;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['url'] = $this->url;

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		foreach ($this->variables as $variableKey => $variable) {
			$data['variables'][$variableKey] = $variable->toArray();
		}

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function addVariable(string $key, ServerVariable $variable): void
	{
		$this->variables[$key] = $variable;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return ServerVariable[]
	 */
	public function getVariables(): array
	{
		return $this->variables;
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
