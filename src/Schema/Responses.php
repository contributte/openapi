<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Responses
{

	/** @var Response[]|Reference[] */
	private array $responses = [];

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param array<string, mixed[]|mixed> $data
	 */
	public static function fromArray(array $data): Responses
	{
		$responses = new Responses();

		foreach ($data as $key => $responseData) {
			if (!is_array($responseData)) {
				continue;
			}

			if (isset($responseData['$ref'])) {
				$responses->setResponse($key, Reference::fromArray($responseData));
			} else {
				$responses->setResponse($key, Response::fromArray($responseData)); // @phpstan-ignore argument.type
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
