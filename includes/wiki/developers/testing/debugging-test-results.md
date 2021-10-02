Debugging test results
======================

If any of the tests fail, the runner will immediately stop at that point, and
runner artifacts are available at:

- ```<docrooot>/tests/_output/phpcs.txt``` for linting results.
- ```<docrooot>/tests/_output/``` for unit and functional test results.
- ```<docrooot>/log/``` for PHP, API logs.

You will also be able to see the output from the runner on ```stdout``` when
you execute the runner, where you will usually see any error details.
