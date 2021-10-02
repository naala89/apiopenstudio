Rebasing
========

**Note:** only do this on a feature branch if you are the only developer working
on the feature branch, otherwise you will break the history for all other developers working on
that branch.

This is safe to do on develop amd master in your fork, because you
will never be working on that and will always want the latest updates to those
branches,

Rebase develop
--------------

This will bring in all the latest changes from the main develop branch into
your develop branch and then replay all your changes on top of that,

    git checkout develop
    git fetch upstream develop
    git rebase upstream/develop
    git push origin develop

Rebasing your feature branch off develop
----------------------------------------

This is useful to do before creating a merge request, because it will resolve
any conflicts that you may have with the merge request. Rebase will ensure that
git replays all of your commits on top of the latest code in develop:

Fetch and rebase your develop branch as above, then:

    git checkout feature/my-branch
    git fetch upstream develop
    git rebase upstream/develop
    git push origin feature/my-branch
