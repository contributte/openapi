<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Info
{

	private string $title;

	private ?string $summary = null;

	private ?string $description = null;

	private ?string $termsOfService = null;

	private ?Contact $contact = null;

	private ?License $license = null;

	private string $version;

	private ?VendorExtensions $vendorExtensions = null;

	public function __construct(string $title, string $version)
	{
		$this->title = $title;
		$this->version = $version;
	}

	/**
	 * @param mixed[] $data
	 */
	public static function fromArray(array $data): Info
	{
		/** @var string $title */
		$title = $data['title'];
		/** @var string $version */
		$version = $data['version'];
		$info = new Info($title, $version);
		/** @var string|null $summary */
		$summary = $data['summary'] ?? null;
		$info->setSummary($summary);
		$info->setDescription($data['description'] ?? null);
		$info->setTermsOfService($data['termsOfService'] ?? null);
		$license = $data['license'] ?? null;
		$info->setLicense($license !== null ? License::fromArray($license) : null);
		$contact = $data['contact'] ?? null;
		$info->setContact($contact !== null ? Contact::fromArray($contact) : null);
		$info->setVendorExtensions(VendorExtensions::fromArray($data));

		return $info;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];
		$data['title'] = $this->title;

		if ($this->description !== null) {
			$data['description'] = $this->description;
		}

		if ($this->summary !== null) {
			$data['summary'] = $this->summary;
		}

		if ($this->termsOfService !== null) {
			$data['termsOfService'] = $this->termsOfService;
		}

		if ($this->contact !== null) {
			$data['contact'] = $this->contact->toArray();
		}

		if ($this->license !== null) {
			$data['license'] = $this->license->toArray();
		}

		$data['version'] = $this->version;

		if ($this->vendorExtensions !== null) {
			$data = array_merge($data, $this->vendorExtensions->toArray());
		}

		return $data;
	}

	public function setSummary(?string $summary): void
	{
		$this->summary = $summary;
	}

	public function setDescription(?string $description): void
	{
		$this->description = $description;
	}

	public function setTermsOfService(?string $termsOfService): void
	{
		$this->termsOfService = $termsOfService;
	}

	public function setContact(?Contact $contact): void
	{
		$this->contact = $contact;
	}

	public function setLicense(?License $license): void
	{
		$this->license = $license;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function getSummary(): ?string
	{
		return $this->summary;
	}

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function getTermsOfService(): ?string
	{
		return $this->termsOfService;
	}

	public function getContact(): ?Contact
	{
		return $this->contact;
	}

	public function getLicense(): ?License
	{
		return $this->license;
	}

	public function getVersion(): string
	{
		return $this->version;
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
