# OpenAPI reference

## Schema objects

The package provides [schema objects](../src/Schema):

- `Callback`, `Components`, `Contact`, `Example`, `ExternalDocumentation`, `Header`, `Info`, `License`, `Link`, and `MediaType`
- `OAuthFlow`, `OpenApi`, `Operation`, `Parameter`, `PathItem`, `Paths`, `Reference`, `RequestBody`, and `Response`
- `Responses`, `Schema`, `SecurityRequirement`, `SecurityScheme`, `Server`, `ServerVariable`, and `Tag`

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
