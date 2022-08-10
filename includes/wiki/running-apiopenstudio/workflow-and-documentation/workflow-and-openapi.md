Workflow and OpenAPI
====================

[OpenAPI][openapi] has become the current standard for API definitions and
documentation. It is very important from the consumers and developers
perspective to have up-to-date and accurate documentation on all API resources.

Ideally, a business and its developers will be following an API-First approach
to developing an API ecosystem. This is where there is an assumption that the
API will be consumed by mobile apps and single page websites. Therefore, the API
becomes a first-class citizen in the development process and is designed and
created before any development is done on the applications or websites.

However, this is not always practical, due to existing legacy code or not enough
time to follow due processes. This can potentially lead to a fragmented and
inconsistent API, with nonexistent or bad documentation.

ApiOpenStudio has been designed to be agnostic and un-opinionated about both
approaches, can accommodate both workflows and make things as easy and
streamlined as possible. ApiOpenStudio allows you to:

* Import an OpenAPI document, and will:
    * Update the documentation for all associated applications and resources.
    * Automatically generate stubs in ApiOpenStudio for the resources that do not
      exist yet
* Developers (with the necessary application access rights) can automatically
  generate base OpenAPI documentation for applications and its resources.
* Developers (with the necessary application access rights) to edit and upload
  the existing documentation in [Swagger editor][swagger_editor].
* All users with access right to an application to view the OpenAPI
  documentation for resources in that application, using
  [Swagger UI][swagger_ui.

ApiOpenStudio currently allows full documentation workflows with
[OpenAPI 2.0][openapi_2_0] and [OpenAPI 3.0.3][openapi_3_0_3].

ApiOpenStudio internally groups the resources against the parent application. So
that documentation can be automatically generated for an application.

[openapi]: https://swagger.io/specification/

[swagger_editor]: https://swagger.io/tools/swagger-editor/

[swagger_ui]: https://swagger.io/tools/swagger-ui/

[openapi_2_0]: https://swagger.io/specification/v2/

[openapi_3_0_3]: https://swagger.io/specification/