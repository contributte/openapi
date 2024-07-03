<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Response
{

	private string $description;

	/** @var Header[]|Reference[] */
	private array $headers = [];

	/** @var MediaType[]|null */
	private ?array $content = null;

	/** @var Link[]|Reference[] */
	private array $links = [];

	private ?VendorExtensions $vendorExtensions = null;

	public function __construct(string $description)
	{
		$this->description = $description;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Response
	{
		$response = new Response($data['description']);

		foreach ($data['headers'] ?? [] as $key => $headerData) {
			if (isset($headerData['$ref'])) {
				$response->setHeader($key, Reference::fromArray($headerData));
			} else {
				$response->setHeader($key, Header::fromArray($headerData));
			}
		}

		if (isset($data['content'])) {
			$response->content = [];
		}

		foreach ($data['content'] ?? [] as $key => $contentData) {
			$response->setContent($key, MediaType::fromArray($contentData));
		}

		foreach ($data['links'] ?? [] as $key => $linkData) {
			if (isset($linkData['$ref'])) {
				$response->setLink($key, Reference::fromArray($linkData));
			} else {
				$response->setLink($key, Link::fromArray($linkData));
			}
		}

		$response->setVendorExtensions(VendorExtensions::fromArray($data));

		return $response;
	}

	public function setContent(string $type, MediaType $mediaType): void
	{
		$this->content[$type] = $mediaType;
	}

	public function setHeader(string $key, Header|Reference $header): void
	{
		$this->headers[$key] = $header;
	}

	public function setLink(string $key, Link|Reference $link): void
	{
		$this->links[$key] = $link;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['description'] = $this->description;

		foreach ($this->headers as $key => $header) {
			$data['headers'][$key] = $header->toArray();
		}

		if ($this->content !== null) {
			$data['content'] = array_map(static fn(MediaType $mediaType): array => $mediaType->toArray(), $this->content);
		}

		foreach ($this->links as $key => $link) {
			$data['links'][$key] = $link->toArray();
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
