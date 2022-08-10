Running a test build locally using GitLab runner
================================================

Ensure that you have set up the GitLab runner locally. See
[Setting up a local GitLab runner][setup_gitlab_runner].

**Note:** This will run the test against locally committed code (it does not
need to be pushed). Changes to `.gitlab-ci.yml` do not need to be committed for
local tests.

Run your `gitlab-runner` command inside your `apiopenstudio` codebase.

Run all pipelines tests on all PHP versions
-------------------------------------------

    $ gitlab-runner exec docker

Run all pipelines tests on php 7.1
----------------------------------

    $ gitlab-runner exec docker test-7.4

Run all pipelines tests on php 8.0
----------------------------------

    $ gitlab-runner exec docker test-8.0

Run a specific test on php 7.4
------------------------------

Edit the `.gitlab-ci.yml` file, Edit the final line in the `test-7.4` section
that calls `CodeCeption`, so that it runs your selected test, example:

Edit the line:

    - ./vendor/bin/codecept run --env ci api

To run your individual `Cept` test:

    - ./vendor/bin/codecept run --env ci api <MyTestCept>

Then run:

    $ gitlab-runner exec docker test-7.4

Run a specific test on php 8.0
------------------------------

Edit the `.gitlab-ci.yml` file, Edit the final line in the `test-8.0` section
that calls `CodeCeption`, so that it runs your selected test, example:

Edit the line:

    - ./vendor/bin/codecept run --env ci api

To run your individual `Cept` test:

    - ./vendor/bin/codecept run --env ci api <MyTestCept>

Then run:

    $ gitlab-runner exec docker test-8.0

Runner issues
-------------

### I see `Error loading key "(stdin)": invalid format`

This is caused by a missing `SSH_PRIVATE_KEY` (which is usually a pipelines
variable), and results in the following on your command line:

    echo "$SSH_PRIVATE_KEY" | tr -d '\r' | ssh-add -

    Error loading key "(stdin)": invalid format
    [cmd] sh exited 1
    [cont-finish.d] executing container finish scripts...
    [cont-finish.d] done.
    [s6-finish] waiting for services.
    [s6-finish] sending all processes the TERM signal.
    [s6-finish] sending all processes the KILL signal and exiting.
    ERROR: Job failed: exit code 1
    FATAL: exit code 1

You need to add the following to the variables section (make sure you don't
commit this into your fork):

    SSH_PRIVATE_KEY: "-----BEGIN OPENSSH PRIVATE KEY-----\n<key_string>\n-----END OPENSSH PRIVATE KEY-----\n"

Test YAML files
---------------

These are in `/tests/_data/`

Links
-----

- [Setting up a local GitLab runner][setup_gitlab_runner]

[setup_gitlab_runner]: /developers/testing/setting-up-local-gitlab-runner
