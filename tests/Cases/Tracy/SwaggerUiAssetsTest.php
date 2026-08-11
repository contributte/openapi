<?php declare(strict_types = 1);

namespace Tests\Cases\Tracy;

use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

/**
 * The panel inlines Swagger UI into a host page, so its stylesheet has to stay
 * confined to the debug bar. These tests guard the outcome of
 * tools/update-swagger-ui.php, which is where the confinement is produced.
 */
class SwaggerUiAssetsTest extends TestCase
{

	private const ASSETS = __DIR__ . '/../../../src/Tracy/templates/assets';

	private const SCOPE = '#tracy-debug';

	/**
	 * The oldest Swagger UI that renders OpenAPI 3.1, which the library supports.
	 */
	private const MINIMAL_VERSION = 5;

	public function testBundleRendersTheVersionsTheLibrarySupports(): void
	{
		$bundle = $this->read('swagger-ui-bundle.js');

		Assert::match(
			'#PACKAGE_VERSION:"(\d+)\.#',
			$bundle,
			'The bundle states no version the way 5.x does. An older Swagger UI states it differently.',
		);

		preg_match('#PACKAGE_VERSION:"(\d+)\.#', $bundle, $matches);

		Assert::true(
			(int) $matches[1] >= self::MINIMAL_VERSION,
			sprintf('Swagger UI %s renders OpenAPI 3.0 only; 3.1 needs 5.x.', $matches[1]),
		);
	}

	public function testEverySelectorIsConfinedToTheDebugBar(): void
	{
		$unconfined = [];

		foreach ($this->selectors() as $selector) {
			if (!str_contains($selector, self::SCOPE)) {
				$unconfined[] = $selector;
			}
		}

		Assert::same([], $unconfined, 'These selectors would style the host page.');
	}

	/**
	 * Scoping a selector must not move it out of the descendant chain Swagger UI
	 * relies on, so the scope always sits directly in front of .swagger-ui.
	 */
	public function testTheScopeSitsInFrontOfTheSwaggerUiClass(): void
	{
		$misplaced = [];

		foreach ($this->selectors() as $selector) {
			if (!str_contains($selector, self::SCOPE . ' .swagger-ui')) {
				$misplaced[] = $selector;
			}
		}

		Assert::same([], $misplaced);
	}

	private function read(string $file): string
	{
		$path = self::ASSETS . '/' . $file;

		Assert::true(is_file($path), sprintf('%s is missing.', $file));

		return (string) file_get_contents($path);
	}

	/**
	 * Selectors of the stylesheet, excluding keyframe positions, which name steps
	 * of an animation rather than elements.
	 *
	 * @return list<string>
	 */
	private function selectors(): array
	{
		$css = $this->read('swagger-ui.css');

		// Drop keyframe blocks wholesale: their positions ("from", "50%") are not
		// selectors, and the rules they contain are reached through the animation.
		$css = (string) preg_replace('#@keyframes[^{]*\{(?:[^{}]*\{[^{}]*\})*[^{}]*\}#', '', $css);
		$css = (string) preg_replace('#/\*.*?\*/#s', '', $css);

		preg_match_all('#(?:^|[{}])([^{}]+)\{#', $css, $matches);

		$selectors = [];

		foreach ($matches[1] as $prelude) {
			$prelude = trim($prelude);

			// Conditional groups carry a condition, not a selector; their contents
			// are matched separately by the same pass.
			if (str_starts_with($prelude, '@')) {
				continue;
			}

			foreach (explode(',', $prelude) as $selector) {
				$selector = trim($selector);

				if ($selector !== '') {
					$selectors[] = $selector;
				}
			}
		}

		Assert::notSame([], $selectors, 'No selectors were found, so these tests prove nothing.');

		return $selectors;
	}

}

(new SwaggerUiAssetsTest())->run();
