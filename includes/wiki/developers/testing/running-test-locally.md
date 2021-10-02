Running a test build locally
============================

### Initial setup

The only thing required is an SSH private key, which is normally provided in the
GitLab config. You can copy the value of a private key from your own ```.ssh```
directory, e.g.:

    cat ~/.ssh/id_rsa

Edit ```.gitlab-ci.yml```. Add an ```SSH_PRIVATE_KEY``` variable to
```test.variables```.

e.g.:

    SSH_PRIVATE_KEY: "-----BEGIN OPENSSH PRIVATE KEY-----\n<key_string>\n-----END OPENSSH PRIVATE KEY-----\n"

Run a CI job
------------

**Note:** Code changes will not appear in the ci tests, unless you at least
commit locally.

See [Setting up a local GitLab runner][setup_gitlab_runner] for details on how
to set up a local GitLab runner instance.

Once set up, you can run the tests like so:

    $ gitlab-runner exec docker tests

Test YAML files
---------------

These are in ```/tests/_data/```

[setup_gitlab_runner]: /developers/testing/setting-up-local-gitlab-runner
