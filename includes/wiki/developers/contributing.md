Contributing
============

All development work and maintenance is performed in the [GitLab](https://gitlab.com/john89/api_open_studio) repository.

The [GitHub](https://github.com/naala89/apiopenstudio) repository is only a mirror for visibility.

Please ensure that all work is done in GitLab.

Process
-------

Create a clone from the [GitLab](https://gitlab.com/john89/api_open_studio) repository. 
All Pull requests should be made against the develop branch.

    git clone --branch develop git@gitlab.com:john89/api_open_studio.git

To set up a local Docker instance of ApiOpenStudio, please visit [Developer environment](https://wiki.apiopenstudio.com/installation/docker/developer-environment.html).

Once you are happy with your changes, you can create a patch for other developers to validate in your ticket:

    git diff develop > <ticket_number>-<changes-summary>.patch

Once that is all accepted, create a pull request of your changes to the develop branch.

This will be validated by the maintainers and if accepted, will be merged.

Wiki
----

All wiki text is stored in markdown format in ```src/wiki```.

We use bookdown to compile the markdown to html.

So any changed that you want to make to the wiki will be instantly visible once merged.

The develop branch wiki is deployed to https://dev.wiki.apiopenstudio.com and master is deployed to https://wiki.apiopenstudio.com.

PhpDoc
------

All merges and commits to the develop and master branches will trigger phpdoc to scan the files and update the phpdoc sites.

Develop branch phpdoc is deployed to https://dev.phpdoc.apiopenstudio.com.

Master branch phpdoc is deployed to https://phpdoc.apiopenstudio.com.

Linting and Coding standards
----------------------------

We adhere to PSR12 standards. See [Developer environment](https://wiki.apiopenstudio.com/installation/docker/developer-environment.html)
for details on using a docker for spinning up a phpdoc local site and linting for simplicity.

All commits and merges go through a linting process, using phpcs.
If the linting fails, the CI will not deploy the phpdoc or wiki changes.

squizlabs/phpcs is defined in the composer.json file. Before final PR, you should run:

    ./vendor/bin/phpcs --standard=PSR12 includes/ src/scss/ src/js/ public/*.php public/admin/*.php *.js
