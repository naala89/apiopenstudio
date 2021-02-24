Coding standards
================

Codesniffer
-----------

The GitLab pipelines runner will run phpcs against any commit or merge resquest.
Any Merge request or commit that fails the phpcs test will not be accepted.
So it is worth running phpcs locally before any commit.

`php_codesniffer` will be installed by composer. The standard used is PSR12.

After `composer install` has run, the following command will test your code locally:

    ./vendor/bin/phpcs --standard=PSR12 includes/ src/scss/ src/js/ public/*.php public/admin/*.php *.js

Line length
-----------

A line of code should not exceed 120 characters.

Variables
---------

* Local variables should be camel case.
* Class names should be camel case and begin with a capital letter.
* Class methods should be camel case and begin with a lower case letter.

Processors
----------

* machine names should be snake case.
* names should be upper case first.

Database
--------

* Table names should be snake case.
* Column names should be snake case.

Arrays
------

* Use the short syntax of [] instead of array().

Constants
--------

* all constants and reserved constants (e.g. true, false, null) should be in lower case.

Get and Post parameters
-----------------------

* get and post params should be in snake case. 

Try/catch and if/else
---------------------

The following curly brackets should not be on a new line. e.g.:

    if (a == b) {
        // Do something.
    } else {
        // Do something else.
    }
    
    try {
        // Do something.
    } catch(Excption $e) {
        // Handle the expection.
    }

PHPDOC
------

Standard PHPDOC rules apply.

### file comment

Should be in the following format:

    /**
     * File description.
     *
     * @package ...
     */

### Function comments

Should be in the following format:

    /**
     * Short description.
     * 
     * Long description(optional).
     *
     * @param type $var Parameter comment.
     *
     * @return type Comment.
     * 
     * @throws \Apiopenstudio\Core\ApiException Exception comment.
     */

### Function declarations

Type hints are not required (to accommodate mixed types), but are recommended.
The curly brackets must be on a new line.

e.g.:

    public function functionName(string $a, int $b, $c)
    {
    }