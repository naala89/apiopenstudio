Introduction
============

This is an optional setup, mainly for developers amd contributors.

The wiki markdown is contained in ```includes/wiki```, and compiled into HTML
using bookdown.

This is done on purpose, to maintainers maintainers of the code quick and easy access to the wiki if they make any changes.

The wiki is also not tightly coupled to the repositories, but deployed to a standalone server in HTML format.

You can setup a serve of the wiki easily through the docker, however the virtual
host settings are additional offered here.

Before you serve the eiki, you will need to compile the wiki. This is donw through bookdown.

Ensure that you have already run ```composer install```.

from the project root, run:

    ./vendor/bin/bookdown includes/wiki/bookdown.json

This will compile and place the reault markup in ```public/wiki```.
