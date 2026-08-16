![](https://heatbadger.now.sh/github/readme/contributte/openapi/)

<p align=center>
  <a href="https://github.com/contributte/openapi/actions"><img src="https://badgen.net/github/checks/contributte/openapi/master?cache=300"></a>
  <a href="https://codecov.io/gh/contributte/openapi"><img src="https://badgen.net/codecov/c/github/contributte/openapi"></a>
  <a href="https://packagist.org/packages/contributte/openapi"> <img src="https://badgen.net/packagist/dm/contributte/openapi"> </a>
  <a href="https://packagist.org/packages/contributte/openapi"> <img src="https://badgen.net/packagist/v/contributte/openapi"> </a>
</p>
<p align=center>
  <a href="https://packagist.org/packages/contributte/openapi"><img src="https://badgen.net/packagist/php/contributte/openapi"></a>
  <a href="https://github.com/contributte/openapi"><img src="https://badgen.net/github/license/contributte/openapi"></a>
  <a href="https://bit.ly/ctteg"><img src="https://badgen.net/badge/support/gitter/cyan"></a>
  <a href="https://bit.ly/cttfo"><img src="https://badgen.net/badge/support/forum/yellow"></a>
  <a href="https://contributte.org/partners.html"><img src="https://badgen.net/badge/become/a%20patron/F96854"></a>
</p>

<p align=center>
Website 🚀 <a href="https://contributte.org">contributte.org</a> | Contact 👨🏻‍💻 <a href="https://f3l1x.io">f3l1x.io</a> | Twitter 🐦 <a href="https://twitter.com/contributte">@contributte</a>
</p>

Pure PHP OpenAPI 3.0 implementation for Nette Framework.

## Versions

| State  | Version | Branch   | Nette | PHP     |
|--------|---------|----------|-------|---------|
| dev    | `^0.2`  | `master` | 4.0+  | `>=8.2` |
| stable | `^0.1`  | `master` | 4.0+  | `>=8.1` |

## Installation

To install the latest version of `contributte/openapi` use [Composer](https://getcomposer.org).

```bash
composer require contributte/openapi
```

## Usage

Create a document with schema objects and export it through `toArray()`.

```php
use Contributte\OpenApi\Schema\Info;
use Contributte\OpenApi\Schema\OpenApi;
use Contributte\OpenApi\Schema\Operation;
use Contributte\OpenApi\Schema\PathItem;
use Contributte\OpenApi\Schema\Paths;
use Contributte\OpenApi\Schema\Response;
use Contributte\OpenApi\Schema\Responses;

$responses = new Responses();
$responses->setResponse('200', new Response('OK'));

$operation = new Operation($responses);
$operation->setSummary('List users');

$path = new PathItem();
$path->setOperation(PathItem::OPERATION_GET, $operation);

$paths = new Paths();
$paths->setPathItem('/users', $path);

$openApi = new OpenApi('3.0.3', new Info('My API', '1.0.0'), $paths);

header('Content-Type: application/json');
echo json_encode($openApi->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
```

The resulting JSON document has `openapi: "3.0.3"`, an `info` object for `My API` version `1.0.0`, and a `/users` GET operation with a `200` response. See [the local reference](.docs/README.md) for the schema inventory and Tracy panel.


## Development

See [how to contribute](https://contributte.org/contributing.html) to this package.

This package is currently maintained by these authors.

<a href="https://github.com/f3l1x">
  <img width="80" height="80" src="https://avatars2.githubusercontent.com/u/538058?v=3&s=80">
</a>

-----

Consider to [support](https://contributte.org/partners.html) **contributte** development team.
Also thank you for using this package.
