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

Runner issues
-------------

### I see ```Error loading key "(stdin)": invalid format```

This is caused by a missing ```SSH_PRIVATE_KEY``` (which is usually a pipelines
variable), and results in the following on your command line:

    $ echo "$SSH_PRIVATE_KEY" | tr -d '\r' | ssh-add -
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

These are in ```/tests/_data/```

[setup_gitlab_runner]: /developers/testing/setting-up-local-gitlab-runner
