Setting up a local GitLab runner
================================

This is useful for local dev testing of GitLab CI.

Install gitlab-runner
---------------------

On Mac:

    brew install gitlab-runner

Register the runner
-------------------

     gitlab-runner register

Set the URL:

    Please enter the gitlab-ci coordinator URL (e.g. https://gitlab.com/):
    https://gitlab.com/

Set the token:

    Please enter the gitlab-ci token for this runner:
    Fetch from Settings -> CI / CD -> Runners

Give the runner a name:

    Please enter the gitlab-ci description for this runner:
    my-runner-local

Add tags:

    Please enter the gitlab-ci tags for this runner (comma separated):
    local-runner

Select the executor:

    Please enter the executor: docker+machine, kubernetes, custom, docker-ssh, parallels, shell, ssh, virtualbox, docker, docker-ssh+machine:
    docker

The runner will now appear in GitLab: Settings -> CI / CD -> Runners

Run a CI job
------------



Links
-----

* https://docs.gitlab.com/runner/install/docker.html
* https://docs.gitlab.com/runner/install/osx.html
* https://docs.gitlab.com/runner/
* https://medium.com/@umutuluer/how-to-test-gitlab-ci-locally-f9e6cef4f054
