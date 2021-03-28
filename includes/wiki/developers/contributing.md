Contributing
============

All development work and maintenance is performed in the [GitLab][gitlab]
repository.

The [GitHub][github] read-only repository is only a mirror for visibility and
composer.

Please ensure that all work is done in [GitLab][gitlab].

Creating and working on issues
------------------------------

To work on issues, you must ```Request Access``` to the repository.

Visit the [GitLab][gitlab] home page, and click on the ```Request Access```
link:

![Request Access screenshot][request_access_screenshot]

The maintainer will review and grant you access. You will then be able to
create, edit and assign tickets, and create merge requests.

### Reporting an issue

Visit the [ApiOpenStudio issue tracker][issues].

Ensure that your issue is not already reported.

If not, click on "New Issue" to open a new issue.

Add a meaningful Title, and as much information as you can in the Description,
with supporting files.

Submit the issue.

### Working on an issue

Visit [ApiOpenStudio issue tracker][issues] and assign a ticket to yourself

Contributing code changes
-------------------------

* Create a fork of [ApiOpenStudio][gitlab]. This is where you can make your code
  changes.
* Clone the fork.
* Add [ApiOpenStudio][gitlab] as ```upstream``` remote.
* Create your feature branch.
* Do your development and testing.
* Create a merge request from your feature branch
  to [ApiOpenStudio develop branch][gitlab]. Your changes will then be reviewed
  and hopefully merged.

### Create a fork

Login to GitLab and navigate to [ApiOpenStudio][gitlab].

Click on the ```Fork``` button on the top right of the page:

![Create a fork button][create_fork]

This will create a copy of [ApiOpenStudio][gitlab] in your repository.

### Clone the fork

Click on ```Clone``` button on your fork, and download this to local computer.

You should then set [ApiOpenStudio][gitlab] as an extra remote. This will allow
you to regularly rebase and ensure that you have the latest version of master
and develop branch from the main repository:

    cd /path/to/my_clone
    git remote add upstream git@gitlab.com:john89/api_open_studio.git
    git fetch upstream

### Create your feature branch

Ensure that your ```develop``` branch is up to date with ```upstream```:

    git checkout origin develop
    git fetch upstream develop
    git rebase upstream/develop
    git push origin develop

Create your new feature branch:

    git checkout -b feature/nn-description-of-the-feature
    git push origin feature/nn-description-of-the-feature

### Commit and push

Commit any changes that you've made to your branch to your origin (fork).

By prefixing your comment with ```#<ticket_no>``` (e.g. #103), the commit will
be added to your ticket as a comment:

    git commit -a -m "#<ticket_no> <commit_comment>"
    git push origin feature/nn-description-of-the-feature

### Create a Merge Request

Before you make a Merge Request, ensure that you've updated the wiki and any
other documentation to reflect your changes.

Click on ```Merge Requests``` in the left menu in your fork.

Click on the ```New merge request``` button.

Select your source branch.

Target branch is always ```john89/api_open_studio``` ```develop```.

Click on ```Compare branches and continue```.

Review your changes.

Linting and Coding standards
----------------------------

We adhere to PSR12 standards.
See [Developer environment](/installation/docker/developer-environment.html) for
details on using a docker for spinning up a phpdoc local site and linting for
simplicity.

All commits and merges go through a linting process, using phpcs. If the linting
fails, the CI will not deploy the phpdoc or wiki changes.

squizlabs/phpcs is defined in the composer.json file. Before creating a PR, you
should run:

    ./vendor/bin/phpcs --standard=PSR12 includes/ public/*.php tests/api/ tests/_support/Helper/ tests/runner_generate_db.php

Testing
-------

Test and/or write additional tests, see [Testing](/developers/testing.html)...

Feature branch naming conventions
---------------------------------

All feature branches should be prefixed with ```feature/```.

Feature branches should contain the ticket number and a short description.

in the form ```feature/<ticket_no>-description```, e.g.:

    feature/103-my-ticket-title

Rebase off develop
------------------

**Note:** only do this if you are the only developer working on the feature
branch, otherwise you will break the history for all other developers working on
that branch.

This is useful to do before creating a merge request, because it will resolve
any conflicts that you may have with the merge request. Rebase will ensure that
git replays all of your commits on top of the latest code in develop:

Fetch and rebase your develop branch, as above
in ```Create your feature branch```, then:

    git checkout feature/my-branch
    git fetch upstream develop
    git rebase upstream/develop
    git push origin feature/my-branch

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

[issues]: https://gitlab.com/john89/api_open_studio/-/issues

[docker]: https://gitlab.com/john89/api_open_studio_docker

[psr_12]: https://www.php-fig.org/psr/psr-12/

[dev_wiki]: https://dev.wiki.apiopenstudio.com

[prod_wiki]: https://wiki.apiopenstudio.com

[dev_phpdoc]: https://dev.phpdoc.apiopenstudio.com

[prod_phpdoc]: https://phpdoc.apiopenstudio.com

[request_access_screenshot]: images/contributing-request-access.png

[create_fork]: images/create-fork.png
