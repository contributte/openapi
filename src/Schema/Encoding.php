<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Encoding
{

	private ?string $contentType = null;

	/** @var array<string, Header|Reference> */
	private array $headers = [];

	private ?string $style = null;

	private ?bool $explode = null;

	private ?bool $allowReserved = null;

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): self
	{
		$encoding = new Encoding();

		$encoding->contentType = $data['contentType'] ?? null;

		foreach ($data['headers'] ?? [] as $name => $header) {
			if (isset($header['$ref'])) {
				$encoding->addHeader($name, Reference::fromArray($header));
			} else {
				$encoding->addHeader($name, Header::fromArray($header));
			}
		}

		$encoding->style = $data['style'] ?? null;
		$encoding->explode = $data['explode'] ?? null;
		$encoding->allowReserved = $data['allowReserved'] ?? null;
		$encoding->setVendorExtensions(VendorExtensions::fromArray($data));

		return $encoding;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		if ($this->contentType !== null) {
			$data['contentType'] = $this->contentType;
		}

		foreach ($this->headers as $name => $header) {
			$data['headers'][$name] = $header->toArray();
		}

		if ($this->style !== null) {
			$data['style'] = $this->style;
		}

		if ($this->explode !== null) {
			$data['explode'] = $this->explode;
		}

		if ($this->allowReserved !== null) {
			$data['allowReserved'] = $this->allowReserved;
		}

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

	public function addHeader(string $name, Header|Reference $header): void
	{
		$this->headers[$name] = $header;
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
