GaterData
=========

GaterData is an open source product, and contributions are welcome.

At heart, GaterData is a complete en d to end REST API solution.

It enables you to generate API's without the need for coding.
API resources are stored in YAML or JSON format,
which include security business logic and multiple outputs of required.

All administrative tools are available via secure API calls.
However, there is also an admin site that you can use to manage your resources.

GaterData has a heirarchical approach to API resources.

* `account` - this defines a tope level entity, like a client.
* `application` - these belong to an account,
   and are used to group API resources, like a department, project or product.
* `resource` - These are individual API definitions that belong to an application.

Each registered user has a role that assigns them specific access rights to the GaterData.
A user that does not have access rights to an account, application or resource cannot use them.

You can create custom roles if you want to further split consumers into groups.

For more information, visit the wiki at [wiki.gaterdata.com](https://wiki.gaterdata.com) or [phpdoc.gaterdata.com](https://phpdoc.gaterdata.com)

Oberview
========

How can you define programmatic logic without coding?
-----------------------------------------------------

Easy - consider that all programming can be reduced down to semantic blocks, like:

* get a data block
* for each element
    * do this
    * if that
        * do something
    * else
        * do something else
* do another thing
* finish and return result

GaterData has Processors that you use to call each semantic block
and the result of each processor flows into another, like a node tree.

This means that you can define all programmatic logic quickly and easily in a YAML or JSON file.

Granular access
---------------

Access to the GaterData is controlled by user roles.
Although you can also create API resources that do not require user validation,
If you want to create a resource that is open to the general public.

Each registered user must have a role assigned to them

Default roles:

* Administrator
* Account manager
* Application manager
* Developer
* Consumer

Security
--------

Each Resource has a security section.
This enables you to create user validation on all resources.
This includes:

* bearer token
* token
* oauth

More security strategies are coming.

Input
-----

GaterData can fetch data from other endpoints,
like API's on a remote, another GaterData resource or a file on a remote server.
These remote endpoints do not need to be completely open:
a resource can be created to use whatever authentication you require on that endpoint.

API Output
----------

The return result is defined in the `accept` header, i.e.

* application/json
* text/html
* application/xml
* application/plain
* application/octet-stream

So this means that you only need to create an API once, not for each format.
It is up to the client to request whatever format they require.

Multiple outputs
----------------

You can also create multiple output streams to the default response output.
Perhaps you also require an email notification on completion of success or failure of the request,
or want to trigger a process on a remote server.

Architecture
------------

GaterData is created to cater for distributed or single server (monollithic) architecture.
Docker examples have been created and also example configurations for NGINX or Apache servers.

Hello world
===========



Developers and contributors
---------------------------

GaterData is hosted on both GitHub and Gitlab.

If you want to create a standalone processor that users can include in their composer project,
You can do that in any versioning system you like.
If the processor is really popular, we may ask you for your permission to include this in GaterData core.

If you want to contribute to the GaterData code base, all project development occurs on Gitlab, so you will need to create a GitLab account.
Then create a fork of the project, and submit your changes in a PR.
See [wiki.gaterdata.com](https://wiki.gaterdata.com) for more details.
