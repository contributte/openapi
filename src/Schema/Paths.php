<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Paths
{

	/** @var PathItem[]|Reference[] */
	private array $paths = [];

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Paths
	{
		$paths = new Paths();

		foreach ($data as $path => $pathItemData) {
			if (isset($pathItemData['$ref'])) {
				$paths->setPathItem($path, Reference::fromArray($pathItemData));
			} else {
				$paths->setPathItem($path, PathItem::fromArray($pathItemData));
			}
		}

		$paths->setVendorExtensions(VendorExtensions::fromArray($data));

		return $paths;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		foreach ($this->paths as $key => $pathItem) {
			$data[$key] = $pathItem->toArray();
		}

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

	public function setPathItem(string $path, PathItem|Reference $pathItem): void
	{
		$this->paths[$path] = $pathItem;
	}

	public function getPath(string $path): PathItem|Reference|null
	{
		return $this->paths[$path] ?? null;
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
