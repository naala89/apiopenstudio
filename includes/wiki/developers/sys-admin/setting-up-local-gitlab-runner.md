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

Edit configuration for env variables
------------------------------------

In theory, we should be able to set the values in ```config.toml```. However,
there is currently a bug in gitlab-runner, where this does not work.

This would have been, get the config file:

    gitlab-runner list

Edit the ```config.toml``` file and set the variables in the ```environment```
key.

As a work-around, update ```.gitlab-ci.yml``` and paste the following variable
into the root of the ```gitlab-ci.yml``` definition:

    variables:
        CI_MYSQL_ROOT_PASSWORD: <mysql_root_password>
        SSH_PRIVATE_KEY: |
            <private_ssh_key>

### Optional

The following environment variables have default values, but for extra security,
you can define them:

    CI_MYSQL_USERNAME: <mysql_username>
    CI_MYSQL_PASSWORD: <mysql_password>
    CI_ADMIN_NAME: <apiopenstudio_admin_username>
    CI_ADMIN_PASS: <apiopenstudio_admin_password>
    CI_ADMIN_EMAIL: <apiopenstudio_admin_email>

Run a CI job
------------

    gitlab-runner exec docker tests

**Note:** Code changes will not appear in the ci tests, unless you at least
commit locally.

Links
-----

* https://docs.gitlab.com/runner/install/docker.html
* https://docs.gitlab.com/runner/install/osx.html
* https://docs.gitlab.com/runner/
* https://medium.com/@umutuluer/how-to-test-gitlab-ci-locally-f9e6cef4f054
