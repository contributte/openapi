<?php declare(strict_types = 1);

namespace Contributte\OpenApi\Validator;

class Problem
{

	public const LEVEL_WARNING = 'warning';
	public const LEVEL_ERROR = 'error';

	public function __construct(
		private string $level,
		private string $path,
		private string $message,
	)
	{
	}

	public function getLevel(): string
	{
		return $this->level;
	}

	/**
	 * Dot-separated location of the problem within the document, for example
	 * "components.securitySchemes.petstore_auth.type".
	 */
	public function getPath(): string
	{
		return $this->path;
	}

	public function getMessage(): string
	{
		return $this->message;
	}

	public function __toString(): string
	{
		return sprintf('[%s] %s: %s', $this->level, $this->path, $this->message);
	}

}
