Installing, updating and uninstalling
=====================================

Scripts
-------

These are located in the source code, in the `bin/` directory, and composer is
configured to copy these into the `vendor/bin/` directory.

If ApiOpenStudio is installed with composer, these can be executed with:

Core
----

After updating ApiOpenStudio core code with composer, it will probably be necessary to run updates
if installed with composer:

    $ ./vendor/bin/aos-update

Otherwise, you can run:

    $ ./bin/aos-update

Command line help:

    $ ./bin/aos-update --help

3rd party Modules
-----------------

Not all 3rd party modules and processors require installing or updating,
however, some do and maintenance be done over the CLI or Admin GUI.

### Command line

Command line help:

    $ ./bin/aos-modules --help

#### List all modules

If ApiOpenStudio is installed with composer:

    $ ./vendor/bin/aos-modules --list

Otherwise:

    $ ./bin/aos-modules --list

#### List all pending module updates

If ApiOpenStudio is installed with composer:

    $ ./vendor/bin/aos-modules --updates

Otherwise:

    $ ./bin/aos-modules --updates

#### List all uninstalled modules

If ApiOpenStudio is installed with composer:

    $ ./vendor/bin/aos-modules --uninstalled

Otherwise:

    $ ./bin/aos-modules --uninstalled

#### List all installed modules

If ApiOpenStudio is installed with composer:

    $ ./vendor/bin/aos-modules --installed

Otherwise:

    $ ./bin/aos-modules --installed

#### Install module

If ApiOpenStudio is installed with composer:

    $ ./vendor/bin/aos-modules --install "\Fully\Namespaced\Module"

Otherwise:

    $ ./bin/aos-modules --install "\Fully\Namespaced\Module"

#### Update module

If ApiOpenStudio is installed with composer:

    $ ./vendor/bin/aos-modules --update "\Fully\Namespaced\Module"

Otherwise:

    $ ./bin/aos-modules --update "\Fully\Namespaced\Module"

#### Uninstall module

If ApiOpenStudio is installed with composer:

    $ ./vendor/bin/aos-modules --uninstall "\Fully\Namespaced\Module"

Otherwise:

    $ ./bin/aos-modules --uninstall "\Fully\Namespaced\Module"
