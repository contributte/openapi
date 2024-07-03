<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Responses
{

	/** @var Response[]|Reference[] */
	private array $responses = [];

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Responses
	{
		$responses = new Responses();

		foreach ($data as $key => $responseData) {
			if (isset($responseData['$ref'])) {
				$responses->setResponse((string) $key, Reference::fromArray($responseData));
			} else {
				$responses->setResponse((string) $key, Response::fromArray($responseData));
			}
		}

		$responses->setVendorExtensions(VendorExtensions::fromArray($data));

		return $responses;
	}

	public function setResponse(string $key, Response|Reference $response): void
	{
		$this->responses[$key] = $response;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		foreach ($this->responses as $key => $response) {
			if ($key === 'default') {
				continue;
			}

			$data[$key] = $response->toArray();
		}

		// Default response last
		if (isset($this->responses['default'])) {
			$data['default'] = $this->responses['default']->toArray();
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
