Setting up a local GitLab runner
================================

This is useful for local dev testing before making a merge request.

Mount a config volume
---------------------

On Mac:

    sudo curl --output /usr/local/bin/gitlab-runner \
    https://gitlab-runner-downloads.s3.amazonaws.com/latest/binaries/gitlab-runner-darwin-amd64
    
    sudo chmod +x /usr/local/bin/gitlab-runner
    
    cd ~
    gitlab-runner install
    gitlab-runner start

Links
-----

* https://docs.gitlab.com/runner/install/docker.html
* https://docs.gitlab.com/runner/install/osx.html
* https://docs.gitlab.com/runner/