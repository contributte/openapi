<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Responses
{

	/** @var Response[]|Reference[] */
	private array $responses = [];

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param array<string, mixed[]> $data
	 */
	public static function fromArray(array $data): Responses
	{
		$responses = new Responses();

		foreach ($data as $key => $responseData) {
			if (isset($responseData['$ref'])) {
				$responses->setResponse($key, Reference::fromArray($responseData));
			} else {
				/** @var array{description: string, headers?: array<string, mixed[]>, content?: array<string, mixed[]>, links?: array<string, mixed[]>} $response */
				$response = $responseData;
				$responses->setResponse($key, Response::fromArray($response));
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
