Creating and Maintaining 3rd party packages
===========================================

Setting up your processor project
---------------------------------

### Project

Your new project should be in its own repository, e.g.

    https://gitlab.com/apiopenstudio/example-processor

#### Directory structure

The basic requirements are a ```composer.json``` file in the extension directory
root, and your code in a ```src``` directory

    ├── my_project
        └── composer.json
        └── src
            └── ...
        ...

#### composer.json

    {
        "name": "<namespace>/<project_name>",
        "description": "Project description",
        "type": "library",
        "autoload": {
            "psr-4": {
                "NameSpace\\": "src/"
            }
        }
    }

Minimum requirements are:

* ```name``` - this is the namespace and project name of your extension. For
  example:
    * ```"name": "my_extension/my_processor"``` will be installed in
      ```vendor/my_extension/my_processor```.
* ```description``` - the description of your plugin.
* ```type``` - ```library``` would be the usual type, but can be any of the
  following:
    * ```library```
    * ```project```
    * ```metapackage```
    * ```composer-plugin```
* ```autoload``` - this tells the PHP autoloader where to look for your files.
  The namespacing reference tells composer to search in the ```src/```
  directory.

#### The src directory

This directory will contain your source class/es, e.g.

    src/MyProcessor.php

#### Configuration ($details['machineName'])

The main difference between core processors and 3rd party, is the
```machineName``` (in the ```$details``` attribute).

By default, core will translate the machineName to the class namespace. However,
this is not possible with 3rd party extensions, where the namespace is unknown.

Therefore, to help users know how to call your extension in their resources,
please give the machineName the full namespace & class, e.g.

    protected array $details = [
        ...
        'machineName' => 'MyProject\\MyProcessor',
        ...
    ];
