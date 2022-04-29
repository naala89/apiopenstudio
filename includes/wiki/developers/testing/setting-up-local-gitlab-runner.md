Setting up a local GitLab runner
================================

This is useful for local dev testing of GitLab CI.

Install gitlab-runner
---------------------

See [install][all_install] for platform specific GitLab runner installation.

### Mac OSx

    brew install gitlab-runner

### Windows

1. Create a folder somewhere in your system, ex.: ```C:\GitLab-Runner```.
2. Download the binary for [64-bit][windows_64_bit] or [32-bit][windows_32_bit]
and put it into the folder you created. The following assumes you have renamed
the binary to ```gitlab-runner.exe``` (optional). You can download a binary for
every available version as described in
[download any other tagged release][windows_other_tagged_release].
3. Make sure to restrict the ```Write``` permissions on the GitLab Runner
directory and executable. If you do not set these permissions, regular users can
replace the executable with their own and run arbitrary code with elevated
privileges. 
4. Run an [elevated command prompt][windows_elevated_prompt] to register.

### Linux

To download the appropriate package for your system:

1. Find the latest file name and options at
[GitLab Runner Latest][linux_gitlab_runner_latest]. 
2. Choose a version and download a binary, as described in the documentation for
[downloading any other tagged releases][linux_other_tagged_release] for bleeding
edge GitLab Runner releases.

For Debian or Ubuntu:

    curl -LJO "https://gitlab-runner-downloads.s3.amazonaws.com/latest/deb/gitlab-runner_${arch}.deb"

For CentOS or Red Hat Enterprise Linux:

    curl -LJO "https://gitlab-runner-downloads.s3.amazonaws.com/latest/rpm/gitlab-runner_${arch}.rpm"

#### Install

Install the package for your system as follows.

For Debian or Ubuntu:

    dpkg -i gitlab-runner_<arch>.deb

For CentOS or Red Hat Enterprise Linux:

    rpm -i gitlab-runner_<arch>.rpm

Register the runner
-------------------

Registering a runner is the process that binds the runner with one or more
GitLab instances.

See [register][all_register] for details on platform specific register commands.

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
    apiopenstudio-runner

Select the executor:

    Please enter the executor: docker+machine, kubernetes, custom, docker-ssh, parallels, shell, ssh, virtualbox, docker, docker-ssh+machine:
    docker

The runner will now appear in GitLab: Settings -> CI / CD -> Runners

Links
-----

* [https://docs.gitlab.com/runner/install](https://docs.gitlab.com/runner/install)
* [https://medium.com/@umutuluer/how-to-test-gitlab-ci-locally-f9e6cef4f054](https://medium.com/@umutuluer/how-to-test-gitlab-ci-locally-f9e6cef4f054)

[windows_64_bit]: https://gitlab-runner-downloads.s3.amazonaws.com/latest/binaries/gitlab-runner-windows-amd64.exe
[windows_32_bit]: https://gitlab-runner-downloads.s3.amazonaws.com/latest/binaries/gitlab-runner-windows-386.exe
[windows_other_tagged_release]: https://docs.gitlab.com/runner/install/bleeding-edge.html#download-any-other-tagged-release
[windows_elevated_prompt]: https://docs.microsoft.com/en-us/powershell/scripting/windows-powershell/starting-windows-powershell?view=powershell-7#with-administrative-privileges-run-as-administrator
[linux_gitlab_runner_latest]: https://gitlab-runner-downloads.s3.amazonaws.com/latest/index.html
[linux_other_tagged_release]: https://docs.gitlab.com/runner/install/bleeding-edge.html#download-any-other-tagged-release
[all_register]: https://docs.gitlab.com/runner/register/index.html
[all_install]: https://docs.gitlab.com/runner/install/
