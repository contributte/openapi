<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Schema;

class Contact
{

	private ?string $name = null;

	private ?string $url = null;

	private ?string $email = null;

	private ?VendorExtensions $vendorExtensions = null;

	/**
	 * @param array{name?: string, url?: string, email?: string} $data
	 */
	public static function fromArray(array $data): Contact
	{
		$contact = new Contact();
		/** @var string|null $name */
		$name = $data['name'] ?? null;
		$contact->setName($name);
		/** @var string|null $url */
		$url = $data['url'] ?? null;
		$contact->setUrl($url);
		/** @var string|null $email */
		$email = $data['email'] ?? null;
		$contact->setEmail($email);
		$contact->setVendorExtensions(VendorExtensions::fromArray($data));

		return $contact;
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = [];

		if ($this->name !== null) {
			$data['name'] = $this->name;
		}

		if ($this->url !== null) {
			$data['url'] = $this->url;
		}

		if ($this->email !== null) {
			$data['email'] = $this->email;
		}

		return $data;
	}

	public function setName(?string $name): void
	{
		$this->name = $name;
	}

	public function setUrl(?string $url): void
	{
		$this->url = $url;
	}

	public function setEmail(?string $email): void
	{
		$this->email = $email;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function getUrl(): ?string
	{
		return $this->url;
	}

	public function getEmail(): ?string
	{
		return $this->email;
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
