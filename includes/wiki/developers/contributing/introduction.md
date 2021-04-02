Introduction
============

First of all, if you are reading this, welcome to the community and many thanks!

Community contributions is the life-blood of open-source. By contributing:

* You keep the project alive and vibrant.
* You will help to solve issues that others may be facing.
* This will enhance your journey as a developer. Not only will you get a warm
  fluffy feeling from contributing and seeing your changes going live, you will
  also learn so much from other contributors and the community.
* The codebase will always be improving and extending.

All development work and maintenance is performed in the [GitLab][gitlab]
repository.

The [GitHub][github] read-only repository is only a mirror for visibility and
composer. Please ensure that all work is done in [ApiOpenStudio][gitlab].

Working on issues
-----------------

You must be registered as a reporter on [ApiOpenStudio][gitlab] to create and
work on issue tickets.

Contributing to issues
----------------------

All work should be done on a fork of [ApiOpenStudio][gitlab] and the merged to
the main repository.

Wiki
----

All wiki text is stored in markdown format in ```includes/wiki/```.

We use bookdown to compile the markdown to html.

Any changed that you want to make to the wiki will be instantly visible once
merged.

The develop branch wiki is deployed to [dev wiki][dev_wiki].

Master is deployed to [prod wiki][prod_wiki].

PhpDoc
------

All merges and commits to the develop and master branches will trigger phpdoc to
scan the files and update the phpdoc sites.

Develop branch phpdoc is deployed to [dev phpdoc][dev_phpdoc].

Master branch phpdoc is deployed to [prod phpdoc][prod_phpdoc].

[gitlab]: https://gitlab.com/john89/api_open_studio

[github]: https://github.com/naala89/api_open_studio

[dev_wiki]: https://dev.wiki.apiopenstudio.com

[prod_wiki]: https://wiki.apiopenstudio.com

[dev_phpdoc]: https://dev.phpdoc.apiopenstudio.com

[prod_phpdoc]: https://phpdoc.apiopenstudio.com
