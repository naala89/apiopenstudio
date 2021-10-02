Introduction
============

ApiOpenStudio uses both unit and functional tests. These are run during the pull
request and merge phases of the development lifecycle.

From a developers point of view, this means that they can immediately see if the
pull request is unstable and the code will not be able to merge until all tests
pass.

The tests include:

- phpcs linting.
- unit tests (using codeception).
- functional API tests (using codeception).

Links
-----

- [Codeception docs][codeception_docs]

[codeception_docs]: https://codeception.com/docs/
