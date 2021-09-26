Working on issues
=================

* Create a fork of [ApiOpenStudio][gitlab]. This is where you can make your code
  changes.
* Set default branch to develop.
* Clone the fork.
* Add [ApiOpenStudio][gitlab] as ```upstream``` remote.
* Create your feature branch.
* Do your development and testing.
* Create a merge request from your feature branch
  to [ApiOpenStudio develop branch][gitlab]. Your changes will then be reviewed
  and hopefully merged.

Create a fork
-------------

Login to GitLab and navigate to [ApiOpenStudio][gitlab].

Click on the ```Fork``` button on the top right of the page:

![Create a fork button][create_fork]

This will create a copy of [ApiOpenStudio][gitlab] in your repository.

Set default branch to develop
-----------------------------

In your fork, navigate to settings -> Repository.

Set ```Default branch``` to develop.

Create and set the SSH_PRIVATE_KEY variable
-------------------------------------------

This is required so that the GitLab CI runner can run in your fork. The runner
instance needs to install the composer dependencies in order to run.

GitLab is configured to prevent any merge requests being merged until tests are
passed.

This variable is safe and masked, and will never be revealed in your runner
logs.

* Navigate to ```Settings``` -> ```CI/CD```.
* Expand the ```Variables``` section.
* Click on ```Add Variable```.
    * Enter ```SSH_PRIVATE_KEY``` in ```Key```.
    * Generate a new private/public key, especially for this purpose.
      See [Keygen][keygen] for more details.
    * Paste the new private key into ```Value```.
    * Uncheck the ```Protect variable``` flag. This will allow the variable to
      be used in the feature branch CI.
    * Click on ```Add Variable```.
* Click on ```Add Variable```.
    * Paste the DB root password ```Value```.
    * Uncheck the ```Protect variable``` flag. This will allow the variable to
      be used in the feature branch CI.
    * Check the ```Mask variable``` flag. This will mask the variable in the
      logs.
    * Click on ```Add Variable```.

### Optional

For added security, you can add the following custom environment variables for
the CI testing:

* CI_MYSQL_USERNAME
    * DB username
* CI_MYSQL_PASSWORD
    * DB password
* CI_ADMIN_NAME
    * ApiOpenStudio admin username
* CI_ADMIN_PASS
    * ApiOpenStudio admin password
* CI_ADMIN_EMAIL
    * ApiOpenStudio admin email

Clone the fork
--------------

Click on ```Clone``` button on your fork, and download this to local computer
and copy the clone path and clobne to your computer:

    git clone git@gitlab.com:<my_username>/api_open_studio.git

Add [ApiOpenStudio][gitlab] as your upstream remote
---------------------------------------------------

This will allow you to regularly rebase and ensure that you have the latest
version of master and develop branch from the main repository:

    cd /path/to/api_open_studio
    git remote add upstream git@gitlab.com:john89/api_open_studio.git
    git fetch upstream

Create your feature branch
--------------------------

Ensure that your ```develop``` branch is up to date with ```upstream```:

    git checkout develop
    git fetch upstream develop
    git rebase upstream/develop
    git push origin develop

Create your new feature branch:

    git checkout -b feature/nn-description-of-the-feature
    git push origin feature/nn-description-of-the-feature

Commit and push
---------------

Commit any changes that you've made to your branch to your origin (fork).

By prefixing your comment with ```#<ticket_no>``` (e.g. #103), the commit will
be automatically added to your ticket as a comment:

    git commit -a -m "#<ticket_no> <commit_comment>"
    git push origin feature/nn-description-of-the-feature

Create a Merge Request
----------------------

Before you make a Merge Request, ensure that you've updated the wiki and any
other documentation to reflect your changes.

Click on ```Merge Requests``` in the left menu in your fork.

Click on the ```New merge request``` button.

Select your source branch.

Target branch is always ```john89/api_open_studio``` ```develop```.

Click on ```Compare branches and continue```.

Review your changes and submit.

[gitlab]: https://gitlab.com/john89/api_open_studio

[create_fork]: images/create-fork.png

[keygen]: https://www.ssh.com/ssh/keygen/]
