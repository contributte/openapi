<?php declare(strict_types = 1);

/**
 * Refreshes the Swagger UI assets the Tracy panel inlines.
 *
 * Usage: php tools/update-swagger-ui.php <version>
 *        php tools/update-swagger-ui.php 5.32.12
 *
 * The JavaScript files are stored verbatim. The stylesheet cannot be, because the
 * panel renders inside a host page: Swagger UI would style elements it does not own.
 * Every selector is therefore scoped to the debug bar, which is what makes the
 * committed stylesheet differ from the distributed one.
 *
 * The scoping refuses to guess. A selector it cannot place aborts the run, so a
 * future Swagger UI that styles the page in a new way gets noticed here instead of
 * in someone's application.
 */

const ASSETS = __DIR__ . '/../src/Tracy/templates/assets';

const SCRIPTS = [
	'swagger-ui-bundle.js',
	'swagger-ui-standalone-preset.js',
];

const STYLESHEET = 'swagger-ui.css';

/**
 * The element Tracy wraps the whole debug bar in.
 */
const SCOPE = '#tracy-debug';

/**
 * Selectors matching none of the scoping rules, dropped with the reason why.
 * Keep the list short: every entry is a place where Swagger UI reaches outside
 * the panel, and dropping a rule means the panel renders unlike the standalone UI.
 */
const DROPPED = [
	// StandaloneLayout puts .dark-mode on <html> itself, so this rule would repaint
	// the host application whenever the developer's system asks for a dark theme.
	'html.dark-mode' => 'would repaint the host page',
];

function fail(string $message): never
{
	fwrite(STDERR, 'Error: ' . $message . PHP_EOL);
	exit(1);
}

function download(string $version, string $file): string
{
	$url = sprintf('https://unpkg.com/swagger-ui-dist@%s/%s', $version, $file);
	$contents = @file_get_contents($url);

	if ($contents === false || $contents === '') {
		fail(sprintf('Could not download %s.', $url));
	}

	echo sprintf('  %s (%s B)', $file, number_format(strlen($contents), 0, '.', ' ')), PHP_EOL;

	return $contents;
}

/**
 * Splits a stylesheet into its top-level pieces: at-rules keep their block intact
 * so nested rules travel with them, and every other piece is a single rule.
 *
 * @return list<string>
 */
function splitRules(string $css): array
{
	$rules = [];
	$depth = 0;
	$start = 0;
	$length = strlen($css);
	$quote = null;

	for ($position = 0; $position < $length; $position++) {
		$character = $css[$position];

		// Braces inside strings are data, not structure: url("data:...{...}").
		if ($quote !== null) {
			if ($character === '\\') {
				$position++;
			} elseif ($character === $quote) {
				$quote = null;
			}

			continue;
		}

		if ($character === '"' || $character === "'") {
			$quote = $character;
		} elseif ($character === '{') {
			$depth++;
		} elseif ($character === '}') {
			$depth--;

			if ($depth === 0) {
				$rules[] = substr($css, $start, $position - $start + 1);
				$start = $position + 1;
			} elseif ($depth < 0) {
				fail('Unbalanced braces in the stylesheet.');
			}
		}
	}

	if ($depth !== 0) {
		fail('Unbalanced braces in the stylesheet.');
	}

	$trailing = substr($css, $start);

	// A comment can follow the last rule, as the sourceMappingURL one does. Keep it
	// with the rule before it rather than dropping bytes the distribution shipped.
	$withoutComments = (string) preg_replace('#/\*.*?\*/#s', '', $trailing);

	if (trim($withoutComments) !== '') {
		fail(sprintf('Trailing content outside any rule: %s', substr(trim($withoutComments), 0, 80)));
	}

	if (trim($trailing) !== '') {
		if ($rules === []) {
			fail('The stylesheet holds comments but no rules.');
		}

		$rules[count($rules) - 1] .= $trailing;
	}

	return $rules;
}

/**
 * Prefixes a single selector so it only ever matches inside the debug bar.
 *
 * Swagger UI descends from .swagger-ui in all but a handful of selectors, so the
 * scope goes in front of that class rather than in front of the whole selector:
 * "html.dark-mode .swagger-ui" has to keep its <html> part outermost.
 */
function scopeSelector(string $selector): ?string
{
	$selector = trim($selector);

	if (isset(DROPPED[$selector])) {
		return null;
	}

	$position = strpos($selector, '.swagger-ui');

	if ($position === false) {
		fail(sprintf(
			'Selector "%s" does not descend from .swagger-ui, so it cannot be scoped to %s. '
			. 'Decide what it should do and teach this script about it.',
			$selector,
			SCOPE,
		));
	}

	return substr($selector, 0, $position) . SCOPE . ' ' . substr($selector, $position);
}

/**
 * @return array{string, int, list<string>} the scoped rule, the number of scoped
 *     selectors, and the selectors dropped along the way
 */
function scopeRule(string $rule): array
{
	$braceposition = strpos($rule, '{');

	if ($braceposition === false) {
		fail(sprintf('Rule without a block: %s', substr($rule, 0, 80)));
	}

	$prelude = substr($rule, 0, $braceposition);
	$body = substr($rule, $braceposition);

	// Keyframe selectors ("from", "to", "50%") name positions in an animation, not
	// elements, and the surrounding @keyframes is already scoped by its own name.
	if (str_starts_with(ltrim($prelude), '@keyframes')) {
		return [$rule, 0, []];
	}

	// Conditional groups hold rules of their own; scope those and keep the condition.
	if (str_starts_with(ltrim($prelude), '@')) {
		// The body starts at its opening brace and ends at the matching one.
		$inner = substr($body, 1, -1);

		$scoped = '';
		$count = 0;
		$dropped = [];

		foreach (splitRules($inner) as $nested) {
			[$nestedScoped, $nestedCount, $nestedDropped] = scopeRule($nested);
			$scoped .= $nestedScoped;
			$count += $nestedCount;
			$dropped = array_merge($dropped, $nestedDropped);
		}

		return [$prelude . '{' . $scoped . '}', $count, $dropped];
	}

	$selectors = [];
	$dropped = [];

	foreach (explode(',', $prelude) as $selector) {
		$scoped = scopeSelector($selector);

		if ($scoped === null) {
			$dropped[] = trim($selector);

			continue;
		}

		$selectors[] = $scoped;
	}

	// Every selector was dropped, so the rule has nothing left to apply to.
	if ($selectors === []) {
		return ['', 0, $dropped];
	}

	return [implode(',', $selectors) . $body, count($selectors), $dropped];
}

$version = $argv[1] ?? null;

if ($version === null) {
	fail(sprintf('Usage: php %s <swagger-ui-dist version>', basename(__FILE__)));
}

echo sprintf('Downloading swagger-ui-dist@%s', $version), PHP_EOL;

$downloaded = [];

foreach ([...SCRIPTS, STYLESHEET] as $file) {
	$downloaded[$file] = download($version, $file);
}

echo sprintf('Scoping %s to %s', STYLESHEET, SCOPE), PHP_EOL;

$scoped = '';
$scopedCount = 0;
$droppedSelectors = [];

foreach (splitRules($downloaded[STYLESHEET]) as $rule) {
	[$ruleScoped, $ruleCount, $ruleDropped] = scopeRule($rule);
	$scoped .= $ruleScoped;
	$scopedCount += $ruleCount;
	$droppedSelectors = array_merge($droppedSelectors, $ruleDropped);
}

echo sprintf('  %d selectors scoped', $scopedCount), PHP_EOL;

foreach (array_count_values($droppedSelectors) as $selector => $times) {
	echo sprintf('  dropped "%s" %dx: %s', $selector, $times, DROPPED[$selector]), PHP_EOL;
}

$downloaded[STYLESHEET] = $scoped;

echo 'Writing assets', PHP_EOL;

foreach ($downloaded as $file => $contents) {
	$path = ASSETS . '/' . $file;

	if (file_put_contents($path, $contents) === false) {
		fail(sprintf('Could not write %s.', $path));
	}

	echo sprintf('  %s', $file), PHP_EOL;
}

echo 'Done.', PHP_EOL;
