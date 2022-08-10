Creating and Maintaining 3rd party packages
===========================================

Example processor projects
--------------------------

* [hello_world][hello_world]
* [hello_world_with_install][hello_world_with_install]

Setting up your processor project
---------------------------------

### Project

Your new project should be in its own repository, e.g.

    https://gitlab.com/apiopenstudio/example-processor

#### Directory structure

The basic requirements are a `composer.json` file in the extension
directory root, and your code in a `src` directory

    ├── my_project
        └── src
            └── MyClass.php
                install.php (optional)
                update.php (optional)
        composer.json
        README.md
        LICENSE

### Composer

Composer is used to define:

* Other 3rd party requirements for your project.
* Where to fetch the source code from (and version).
* Where to install the code.

By default, Composer will install everything in directories in the `vendor/`
directory. However, ApiOpenStudio will need to have processors and modules
installed separately to use them. So we need to instruct composer to install
your package to a separate location.

#### Composer package types

Composer does not easily allow installation of packages anywhere other than the
`vemdor\` directory
([How do I install a package to a custom path for my framework?][composer_custom_path]).
There is an implementation for pre-defined package types
(see [Current Supported Package Types][current_supported_package_types]).

However, using [composer-installers-extender][composer_installers_extender], we
can create custom package installation types. This is taken care of by
ApiOpenStudio, and all you need to be aware of is the four package types, the
installation location, and it's usage:

* `apiopenstudio-endpoint-package`
    * Install location: `includes/Security/vendor/`
    * Usage: Processors that handle fetching data from external endpoints and
      their security.
* `apiopenstudio-output-package`
    * Install location: `includes/Output/vendor/`
    * Usage: Processors that handle resource remote or response output .
* `apiopenstudio-processor-package`
    * Install location: `includes/Processor/vendor/`
    * Usage: Standard processors that are used in the processing logic for a
      resource.
* `apiopenstudio-security-package`
    * Install location: `includes/Security/vendor/`
    * Usage: Processors that handle resource security.

#### composer.json

    {
        "name": "<namespace>/<project_name>",
        "description": "Project description",
        "type": "<apiopenstudio_package_type>",
        "keywords": [],
        "autoload": {
            "psr-4": {
                "NameSpace\\": "src/"
            }
        },
    }

Minimum requirements are:

* `name` - this is the namespace and project name of your extension. For
  example:
    * `"name": "my_extension/my_processor"` will be installed in
      `vendor/my_extension/my_processor`.
* `description` - the description of your plugin.
* `type` - `library` would be the usual type, but can be any of the
  following:
    * `library`
    * `project`
    * `metapackage`
    * `composer-plugin`
* `autoload` - this tells the PHP autoloader where to look for your files.
  The namespacing reference tells composer to search in the `src/`
  directory.

#### The src directory

This directory will contain your source class and install/update files, e.g.

    src/MyProcessor.php
    src/install.php
    src/update.php

##### MyProcessor.php

This contains the class for your processor.

##### install.php

This is optional and contains the `install()` and `uninstall()` functions. You
Only need this if your processor needs additional data or configuration to be
done when enabling or disabling it.

##### update.php

This is optional and is only required if there are data updates that need to be
done between your package versions.

#### Configuration ($details['machineName'])

The main difference between core processors and 3rd party, is the
`machineName` (in the `$details` attribute).

By default, core will translate the machineName to the class namespace. However,
this is not possible with 3rd party extensions, where the namespace is unknown.

Therefore, to help users know how to call your extension in their resources,
please give the machineName the full namespace & class, e.g.

    protected array $details = [
        ...
        'machineName' => 'MyProject\\MyProcessor',
        ...
    ];

Links
-----

* [How do I install a package to a custom path for my framework?][composer_custom_path]
* [Current Supported Package Types][current_supported_package_types]
* [composer_installers_extender][composer_installers_extender]
* [hello_world][hello_world] example processor
* [hello_world_with_install][hello_world_with_install] example processor

[composer_custom_path]: https://getcomposer.org/doc/faqs/how-do-i-install-a-package-to-a-custom-path-for-my-framework.md

[current_supported_package_types]: https://github.com/composer/installers#current-supported-package-types

[composer_installers_extender]:https://github.com/oomphinc/composer-installers-extender

[hello_world]: https://gitlab.com/laughing_man77/hello_world

[hello_world_with_install]: https://gitlab.com/laughing_man77/hello_world_with_install