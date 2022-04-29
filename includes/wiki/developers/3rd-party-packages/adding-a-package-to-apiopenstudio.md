Adding a package to ApiOpenStudio
=================================

Including the extension in your project
---------------------------------------

Then run the following command to install the extension:

    composer require example_package/example_processor

Using the extension in resources
--------------------------------

By default, ApiOpenStudio uses the machine_name in processors to find them.
However, in the case of 3rd party extensions, the namespacing will not be
```ApiOpenStudio\{processor,security,output,exndpoint}\<whatever>```... It will
have its own namespace and classname. So instead of the machine name, you need
to use the full namespacing in your resource YAML files. The maintainer should
have already set that for you in the ```machineName``` attribute, e.g.

    processor: MyExtensions\MyProcessor
    ...

Links
-----

* https://getcomposer.org/doc/05-repositories.md
* https://medium.com/@sirajul.anik/composer-require-a-php-package-from-a-local-or-remote-repository-7e641bdbc824
* https://getcomposer.org/doc/04-schema.md#type
* https://getcomposer.org/doc/04-schema.md#autoload
