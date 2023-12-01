# Contributte OpenApi

Pure PHP OpenAPI 3.0 implementation for Nette Framework.

## Content

- [Setup](#setup)
- [OpenAPI](#tracy)
- [Tracy](#tracy)

## Setup

Install package

```bash
composer require contributte/openapi
```

## OpenAPI

- [Callback.php](../src/Schema/Callback.php)
- [Components.php](../src/Schema/Components.php)
- [Contact.php](../src/Schema/Contact.php)
- [Example.php](../src/Schema/Example.php)
- [ExternalDocumentation.php](../src/Schema/ExternalDocumentation.php)
- [Header.php](../src/Schema/Header.php)
- [Info.php](../src/Schema/Info.php)
- [License.php](../src/Schema/License.php)
- [Link.php](../src/Schema/Link.php)
- [MediaType.php](../src/Schema/MediaType.php)
- [OAuthFlow.php](../src/Schema/OAuthFlow.php)
- [OpenApi.php](../src/Schema/OpenApi.php)
- [Operation.php](../src/Schema/Operation.php)
- [Parameter.php](../src/Schema/Parameter.php)
- [PathItem.php](../src/Schema/PathItem.php)
- [Paths.php](../src/Schema/Paths.php)
- [Reference.php](../src/Schema/Reference.php)
- [RequestBody.php](../src/Schema/RequestBody.php)
- [Response.php](../src/Schema/Response.php)
- [Responses.php](../src/Schema/Responses.php)
- [Schema.php](../src/Schema/Schema.php)
- [SecurityRequirement.php](../src/Schema/SecurityRequirement.php)
- [SecurityScheme.php](../src/Schema/SecurityScheme.php)
- [Server.php](../src/Schema/Server.php)
- [ServerVariable.php](../src/Schema/ServerVariable.php)
- [Tag.php](../src/Schema/Tag.php)

## Tracy

![](misc/tracy-panel.png)

```neon
services:
    swaggerPanel:
        class: Contributte\OpenApi\Tracy\SwaggerPanel()
        setup:
            - setSpec([...openapi])
            - setLazySpec([@openapi, getSpec])
            - setUrl('https://petstore.swagger.io/v2/swagger.json')
            - setExpansion()
            - setFilter("role=User")
            - setTitle(MyAPI)

tracy:
	bar:
		- @swaggerPanel
```
