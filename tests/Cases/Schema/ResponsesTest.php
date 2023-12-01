<?php declare(strict_types = 1);

namespace Tests\Cases\Schema;

use Contributte\OpenApi\Schema\Reference;
use Contributte\OpenApi\Schema\Response;
use Contributte\OpenApi\Schema\Responses;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class ResponsesTest extends TestCase
{

	private const ARRAY = [
		'200' => ['description' => self::S200_DESCRIPTION],
		'401' => ['$ref' => self::S401_REFERENCE],
	];

	private const S200_DESCRIPTION = 'Success';

	private const S401_REFERENCE = '#/components/responses/UnauthorizedError';

	private Responses $responses;

	public function testFromArray(): void
	{
		$actual = Responses::fromArray(self::ARRAY);
		Assert::equal($this->responses, $actual);
	}

	public function testToArray(): void
	{
		Assert::equal(self::ARRAY, $this->responses->toArray());
	}

	protected function setUp(): void
	{
		$this->responses = new Responses();
		$this->responses->setResponse('200', new Response(self::S200_DESCRIPTION));
		$this->responses->setResponse('401', new Reference(self::S401_REFERENCE));
	}

}

(new ResponsesTest())->run();
