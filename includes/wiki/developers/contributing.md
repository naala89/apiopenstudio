Contributing
============

All development work and maintenance is performed in the [GitLab][gitlab]
repository.

The [GitHub][github] read-only repository is only a mirror for visibility.

Please ensure that all work is done in [GitLab][gitlab].

Reporting an issue
------------------

Visit the [ApiOpenStudio issue tracker][issues].

Ensure that your issue is not already reported.

If not, click on "New Issue" to open a new issue.

Add a meaningful Title, and as much information as you can in the Description,
with supporting fioles.

Submit the issue.

Working on an issue
-------------------

Visit [ApiOpenStudio issue tracker][issues] and assign a ticket to yourself

Create a clone from the [GitLab][gitlab] repository:

    $ git clone --branch develop git@gitlab.com:john89/api_open_studio.git

This will checkout the main api_open_studio repositopry from GitLab and checkout
the develop branch.

Create your feature branch:

    $ checkout -b <my_feature_branch>

### Feature branch naming conventions

All feature branches should be prefixed with ```feature/```.

Feature branches should c ontain the ticket number and a short description.

in the form ```feature/<ticket_no>-description```, e.g.:

    feature/103-my-ticket-title

Code...

Make any comments that you need in your ticket.

Once you are happy with your changes and it is ready for community testing and
merging:

### Rebase off develop

Rebase your branch off develop (this will ensure that git replays all of your
commits on top of the latest code in develop):

    git checkout feature/103-my-ticket-title
    git fetch origin develop
    git rebase origin/develop
    git push origin feature/103-my-ticket-title

### Test

Run the linting tests and resolve any issues,
see [Linting](/developers/linting.html)...

Test and/or write additional tests, see [Testing](/developers/testing.html)...

### Commit and push

Commit any changes that you've made to your branch.

By prefixing your comment with ```#<ticket_no>``` (e.g. #103), the commit will
be added to your ticket.:

    git commit -a -m "#<ticket_no> <commit_comment>"
    git push origin feature/103-my-ticket-title

### Create a PR

Nasvigate to [merge requests][merge_requests].

Click on ```New merge request```.

Select your source branch.

Target branch is ```develop```.

Click on ```Compare branches and continue```.

Review your changes.

Process
-------

All Pull requests should be made against the develop branch.

To set up a local Docker instance of ApiOpenStudio, please
visit [ApiOpenStudio docker][docker].

Once you are happy with your changes, you can create a patch for other
developers to validate in your ticket:

    git diff develop > <ticket_number>-<changes-summary>.patch

Once that is all accepted, create a pull request of your changes to the develop
branch.

This will be validated by the maintainers and if accepted, will be merged.

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

Linting and Coding standards
----------------------------

We adhere to PSR12 standards. See [Developer environment](/installation/docker/developer-environment.html) for details on
using a docker for spinning up a phpdoc local site and linting for simplicity.

All commits and merges go through a linting process, using phpcs. If the linting
fails, the CI will not deploy the phpdoc or wiki changes.

squizlabs/phpcs is defined in the composer.json file. Before creating a PR, you
should run:

    ./vendor/bin/phpcs --standard=PSR12 includes/ public/*.php tests/api/ tests/_support/Helper/ tests/runner_generate_db.php

[gitlab]: https://gitlab.com/john89/api_open_studio

[github]: https://github.com/naala89/apiopenstudio

[issues]: https://gitlab.com/john89/api_open_studio/-/issues

[merge_requests]: https://gitlab.com/john89/api_open_studio/-/merge_requests

[docker]: https://gitlab.com/john89/api_open_studio_docker

[psr_12]: https://www.php-fig.org/psr/psr-12/

[dev_wiki]: https://dev.wiki.apiopenstudio.com

[prod_wiki]: https://wiki.apiopenstudio.com

[dev_phpdoc]: https://dev.phpdoc.apiopenstudio.com

[prod_phpdoc]: https://phpdoc.apiopenstudio.com
