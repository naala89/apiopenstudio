# ![ApiOpenStudio][logo]

[![License: MPL 2.0][license_logo]][license]

Introduction
============

ApiOpenStudio is an open source product, and contributions are welcome.

At heart, ApiOpenStudio is a complete end-to-end, headless REST API solution.

ApiOpenStudio enables you to generate API's without the need for coding. API
resources are stored in YAML or JSON format, which include security business
logic and multiple outputs of required.

All administrative tools are available via secure API calls.

ApiOpenStudio has a hierarchical approach to API resources.

* `account` - this defines a top-level entity, like a client.
* `application` - these belong to an account and are used to group API
  resources, like a department, project or product.
* `resource` - These are individual API definitions that belong to an
  application.

Each registered user has a role that assigns them specific access rights to the
ApiOpenStudio. A user that does not have access rights to an account,
application or resource cannot use them.

You can create custom roles if you want to further split consumers into groups.

For more information, visit the [Wiki][wiki] or [PHPDoc][phpdoc].

Admin GUI
---------

There is a separate repository/application, which provides a GUI interface:

* [GitLab][admin_gitlab]
* [GitHub][admin_github]

More are planned, to allow more choices of GUI and single-page applications, and
give the community a more choice.

Overview
========

How can you define programmatic logic without coding?
-----------------------------------------------------

Easy - consider that all programming can be reduced down to semantic blocks,
like:

* get a data block
* for each element
    * do this
    * if that
        * do something
    * else
        * do something else
* do another thing
* finish and return result

ApiOpenStudio has Processors that you use to call each semantic block and the
result of each processor flows into another, like a node tree.

This means that you can define all programmatic logic quickly and easily in a
YAML or JSON file.

Granular access
---------------

Access to ApiOpenStudio is controlled by user roles. Although you can also
create API resources that do not require user validation. If you want to create
a resource that is open to the general public.

Each registered user must have a role assigned to them

Default roles:

* Administrator
* Account manager
* Application manager
* Developer
* Consumer

![User roles][user_roles]

Security
--------

Each Resource has a security section. This enables you to create user validation
on all resources. This includes:

* bearer token
* token
* oauth

More security strategies are coming.

Input
-----

ApiOpenStudio can fetch data from other endpoints, like API's on a remote,
another ApiOpenStudio resource or a file on a remote server. These remote
endpoints do not need to be completely open: a resource can be created to use
whatever authentication you require on that endpoint.

Output
------

The return result is defined in the `accept` header, i.e.

* application/json
* text/html
* application/xml
* application/plain
* application/octet-stream

So this means that you only need to create an API once, not for each format. It
is up to the client to request whatever format they require.

Multiple outputs
----------------

You can also create multiple output streams to the default response output.
Perhaps you also require an email notification on completion of success or
failure of the request, or want to trigger a process on a remote server.

Architecture
------------

ApiOpenStudio is created to cater for distributed or single server (monolithic)
architecture. Docker examples have been created and also example configurations
for NGINX or Apache servers.

Quick start
===========

Installation
------------

The quickest way to install ApiOpenStudio is to create a project with composer:

    composer create-project apiopenstudio/apiopenstudio:1.0.0-alpha

Or checkout the repository [GitHub mirror][studio_github]:

    git clone https://github.com/naala89/api_open_studio

Or checkout the main repository [Gitlab][studio_gitlab]:

    git clone https://gitlab.com/john89/api_open_studio

Serve ApiOpenStudio and admin through Docker. See
the [Docker github repo][docker_github] or
the [Docker gitlab repo][docker_gitlab]

### Setup the DB on a standalone server

    cd /var/www/api_open_studio
    ./includes/scripts/install.php

### Setup the DB on a docker instance

    docer exec -it apiopenstudio-php bash
    cd api
    ./includes/scripts/install.php

# Developers and contributors

ApiOpenStudio is hosted on both GitHub and Gitlab (because GitLab is great).

The GitHub repository is purely a read only mirror of the GitLab repository, for
ease of access and its popularity.

If you want to create a standalone processor that users can include in their
composer project, You can do that in any versioning system you like. If the
processor is really popular, we may ask you for your permission to include this
in ApiOpenStudio core.

If you want to contribute to the ApiOpenStudio code base, all project
development occurs on Gitlab, so you will need to create a GitLab account,
create a fork of the project, and submit your changes in a PR. See [wiki][wiki]
for more details.

[license_logo]: https://img.shields.io/badge/License-MPL%202.0-brightgreen.svg

[license]: https://opensource.org/licenses/MPL-2.0

[wiki]: https://wiki.apiopenstudio.com

[phpdoc]: https://phpdoc.apiopenstudio.com

[admin_gitlab]: https://gitlab.com/john89/api_open_studio_admin

[admin_github]: https://github.com/naala89/api_open_studio_admin

[docker_github]: https://github.com/naala89/api_open_studio_docker

[docker_gitlab]: https://gitlab.com/john89/api_open_studio_docker

[studio_github]: https://github.com/naala89/api_open_studio

[studio_gitlab]: https://gitlab.com/john89/api_open_studio

[logo]: includes/wiki/images/api_open_studio_logo_name_colour.png

[user_roles]: includes/wiki/images/user_roles_2.png