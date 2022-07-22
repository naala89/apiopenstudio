Introduction
============

ApiOpenStudio uses both unit and functional tests. These are run during the pull
request and merge phases of the development lifecycle.

Therefore, it is important that you validate that all tests pass before
committing and creating a merge request (otherwise the merge request will fail).

From a developers point of view, this means that they can immediately see if the
pull request is unstable and the code will not be able to merge until all tests
pass.

The tests include:

- phpcs linting.
- unit tests (using codeception).
- functional API tests (using codeception).

Running tests
-------------

Tests are run using [Codeception][codecept_docs] and
[PHP CodeSniffer][php_codesniffer_docs]. You can run these tests using
[GitLab Runner][setup_gitlab_runner] or
[inside a running instance of ApiOpenStudio][run_tests_outside_gitlab_runner].

The best and most effective way is to run tests using `gitlab-runner`, because
this will simulate the exact testing that GitLab pipelines will run.

Links
-----

- [Codeception documentation][codecept_docs]
- [PHP CodeSniffer documentation][php_codesniffer_docs]
- [Setup GitLab Runner][setup_gitlab_runner]
- [Run_tests_locally using GitLab runner][run_tests_locally_using_gitlab_runner]

[codecept_docs]: https://codeception.com/docs/
[php_codesniffer_docs]: https://github.com/squizlabs/PHP_CodeSniffer/wiki
[setup_gitlab_runner]: /developers/testing/setting-up-local-gitlab-runner
[run_tests_outside_gitlab_runner]: /developers/testing/run-codeception-outside-gitlab-runner
[run_tests_locally_using_gitlab_runner]: /developers/testing/running-test-locally-using-gitlab-runner