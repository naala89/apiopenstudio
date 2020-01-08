Setting up a local GitLab runner
================================

This is useful for local dev testing before making a merge request.

Mount a config volume
---------------------

On Mac:

    docker run -d --name gitlab-runner --restart always \
      -v /Users/Shared/gitlab-runner/config:/etc/gitlab-runner \
      -v /var/run/docker.sock:/var/run/docker.sock \
      gitlab/gitlab-runner:latest

Register the runner
-------------------

On Mac:

    docker run --rm -t -i \
      -v /Users/Shared/gitlab-runner/config:/etc/gitlab-runner \
      gitlab/gitlab-runner register

Run the Runner
--------------

    docker run -d --name gitlab-runner --restart always \
        -v /var/run/docker.sock:/var/run/docker.sock \
        --volumes-from gitlab-runner-config \
        gitlab/gitlab-runner:latest

Links
-----

* https://docs.gitlab.com/runner/install/docker.html