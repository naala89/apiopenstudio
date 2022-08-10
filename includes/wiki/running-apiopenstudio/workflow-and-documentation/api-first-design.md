API-First Design
================

This is the **"Happy path"** workflow, where the API is designed before any work
begins on development of the API or the architecture that will consume the APIs.

It is a logical extension of the "Mobile-First" principle. Because the API
design and documentation is generated before any development begins, this means
that both the API and API-consuming architectures can potentially begin at the
same time.

1. Design the new feature
2. Get feedback about the new feature
3. Design the Api:
    1. Standards
    2. Information requirements for use cases
    3. Design the Api resources
        1. Input
        2. Processing
        3. Output
    4. Document the design in OpenAPI
4. Build the API
5. Deploy the API
6. Design and build the API consuming architecture

Some of the process steps are not part of the remit of OpenApiStudio, in fact,
OpenApiStudio won't be used until steps 3.4 (or 4) through to 5. However, OpenApiStudio allows to you to import the
OpenAPI documents, and it will then
automatically generate resource stubs and store the necessary documentation
for them.

Each OpenAPI document should only relate to an application and its resources,
i.e. create separate OpenApi documents for each account/application and its
resources.

It allows you to upload end edit the existing documentation, and will
continuously update the internally stored documentation and generate stubs for
any resources that do nopt exist yet.
