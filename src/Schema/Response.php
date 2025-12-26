<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

use Contributte\OpenApi\Utils\Helpers;

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
		$response = new Response(Helpers::getString($data, 'description'));

		$headers = Helpers::getArrayOrNull($data, 'headers') ?? [];
		foreach ($headers as $key => $headerData) {
			if (is_array($headerData)) {
				if (isset($headerData['$ref'])) {
					$response->setHeader((string) $key, Reference::fromArray($headerData));
				} else {
					$response->setHeader((string) $key, Header::fromArray($headerData));
				}
			}
		}

		$content = Helpers::getArrayOrNull($data, 'content');
		if ($content !== null) {
			$response->content = [];
			foreach ($content as $key => $contentData) {
				if (is_array($contentData)) {
					$response->setContent((string) $key, MediaType::fromArray($contentData));
				}
			}
		}

		$links = Helpers::getArrayOrNull($data, 'links') ?? [];
		foreach ($links as $key => $linkData) {
			if (is_array($linkData)) {
				if (isset($linkData['$ref'])) {
					$response->setLink((string) $key, Reference::fromArray($linkData));
				} else {
					$response->setLink((string) $key, Link::fromArray($linkData));
				}
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
			$data['content'] = array_map(static fn (MediaType $mediaType): array => $mediaType->toArray(), $this->content);
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
