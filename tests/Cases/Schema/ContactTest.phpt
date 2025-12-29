<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\Contact;
use Contributte\Tester\Toolkit;
use Tester\Assert;

require_once __DIR__ . '/../../bootstrap.php';

// Test optional fields
Toolkit::test(static function (): void {
	$contact = new Contact();
	$contact->setName('API Support');
	$contact->setEmail('support@example.com');
	$contact->setUrl('http://www.example.com/support');

	Assert::same('API Support', $contact->getName());
	Assert::same('http://www.example.com/support', $contact->getUrl());
	Assert::same('support@example.com', $contact->getEmail());

	$realData = $contact->toArray();
	$expectedData = [
		'name' => 'API Support',
		'url' => 'http://www.example.com/support',
		'email' => 'support@example.com',
	];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, Contact::fromArray($realData)->toArray());
});

// Test required fields
Toolkit::test(static function (): void {
	$contact = new Contact();

	Assert::null($contact->getName());
	Assert::null($contact->getUrl());
	Assert::null($contact->getEmail());

	$realData = $contact->toArray();
	$expectedData = [];

	Assert::same($expectedData, $realData);
	Assert::same($expectedData, Contact::fromArray($realData)->toArray());
});
