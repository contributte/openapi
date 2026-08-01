<?php declare(strict_types = 1);

namespace Tests\Cases\Validator;

use Contributte\OpenApi\Schema\OpenApi;
use Contributte\OpenApi\Validator\Problem;
use Contributte\OpenApi\Validator\VersionValidator;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class VersionValidatorTest extends TestCase
{

	private const INFO = ['title' => 'Test API', 'version' => '1.0.0'];

	private VersionValidator $validator;

	public function testSupportedDocumentsWithoutProblems(): void
	{
		Assert::same([], $this->validate(['openapi' => '3.0.4', 'info' => self::INFO, 'paths' => []]));
		Assert::same([], $this->validate(['openapi' => '3.1.1', 'info' => self::INFO]));
	}

	public function testUnsupportedVersionIsReported(): void
	{
		$problems = $this->validate(['openapi' => '2.0', 'info' => self::INFO, 'paths' => []]);

		Assert::same(['warning openapi'], $this->summarize($problems));
		Assert::contains('2.0', $problems[0]->getMessage());
	}

	public function testFieldsIntroducedLater(): void
	{
		Assert::same(['warning jsonSchemaDialect'], $this->summarize($this->validate([
			'openapi' => '3.0.4',
			'info' => self::INFO,
			'paths' => [],
			'jsonSchemaDialect' => 'https://json-schema.org/draft/2020-12/schema',
		])));

		Assert::same(['warning webhooks'], $this->summarize($this->validate([
			'openapi' => '3.0.4',
			'info' => self::INFO,
			'paths' => [],
			'webhooks' => ['newPet' => ['summary' => 'New pet']],
		])));
	}

	public function testNestedFieldsIntroducedLater(): void
	{
		Assert::same(['warning info.summary'], $this->summarize($this->validate([
			'openapi' => '3.0.4',
			'info' => self::INFO + ['summary' => 'A short summary'],
			'paths' => [],
		])));

		Assert::same(['warning info.license.identifier'], $this->summarize($this->validate([
			'openapi' => '3.0.4',
			'info' => self::INFO + ['license' => ['name' => 'MIT', 'identifier' => 'MIT']],
			'paths' => [],
		])));
	}

	public function testComponentConstructsIntroducedLater(): void
	{
		Assert::same(['warning components.pathItems'], $this->summarize($this->validate([
			'openapi' => '3.0.4',
			'info' => self::INFO,
			'paths' => [],
			'components' => ['pathItems' => ['Pet' => ['summary' => 'A pet']]],
		])));

		Assert::same(['warning components.securitySchemes.mtls.type'], $this->summarize($this->validate([
			'openapi' => '3.0.4',
			'info' => self::INFO,
			'paths' => [],
			'components' => ['securitySchemes' => ['mtls' => ['type' => 'mutualTLS']]],
		])));
	}

	public function testFieldRequiredBeforeItBecameOptional(): void
	{
		$problems = $this->validate(['openapi' => '3.0.4', 'info' => self::INFO]);

		Assert::same(['error paths'], $this->summarize($problems));
		Assert::same(Problem::LEVEL_ERROR, $problems[0]->getLevel());
	}

	public function testNewerFieldsAreAcceptedInTheirOwnVersion(): void
	{
		Assert::same([], $this->validate([
			'openapi' => '3.1.1',
			'info' => self::INFO + ['summary' => 'A short summary', 'license' => ['name' => 'MIT', 'identifier' => 'MIT']],
			'jsonSchemaDialect' => 'https://json-schema.org/draft/2020-12/schema',
			'webhooks' => ['newPet' => ['summary' => 'New pet']],
			'components' => [
				'pathItems' => ['Pet' => ['summary' => 'A pet']],
				'securitySchemes' => ['mtls' => ['type' => 'mutualTLS']],
			],
		]));
	}

	public function testEveryProblemIsReportedAtOnce(): void
	{
		Assert::same([
			'warning jsonSchemaDialect',
			'warning info.summary',
			'error paths',
		], $this->summarize($this->validate([
			'openapi' => '3.0.4',
			'info' => self::INFO + ['summary' => 'A short summary'],
			'jsonSchemaDialect' => 'https://json-schema.org/draft/2020-12/schema',
		])));
	}

	protected function setUp(): void
	{
		$this->validator = new VersionValidator();
	}

	/**
	 * @param mixed[] $data
	 * @return Problem[]
	 */
	private function validate(array $data): array
	{
		return $this->validator->validate(OpenApi::fromArray($data));
	}

	/**
	 * @param Problem[] $problems
	 * @return string[]
	 */
	private function summarize(array $problems): array
	{
		return array_map(
			static fn (Problem $problem): string => $problem->getLevel() . ' ' . $problem->getPath(),
			$problems
		);
	}

}

(new VersionValidatorTest())->run();
