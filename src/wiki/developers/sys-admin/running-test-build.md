Running a test build locally
============================

edit ```.gitlab-ci.yml```

    $ gitlab-runner exec docker <task>

GitLab variables are not available to locally run runners with exec.
So you must define then in your exec command:

    $ gitlab-runner exec docker <task> --env VAR1="foo" --env VAR2="bar"