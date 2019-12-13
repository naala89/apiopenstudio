[[_TOC_]]

Codesniffer
-----------

Phpcs is declared in the composer file, so after running `composer install`, you can run:

`$ ./vendor/bin/phpcs --standard=gaterdata_phpcs_rulesset.xml includes/`

Variables
---------

* Local variables should be camel case.
* Class names should be camel case and begin with a capital letter.
* Class methods should be camel case and begin with a lower case letter.

Processors
----------

* machine names should be snake case.
* names should be uppper case first.

Database
--------

* Table names should be sname case.
* Column names should be snake case.

Arrays
------

* Use the short syntax of [] instead of array().

Constants
--------

* all constants and reserved constants (like TRUE, NULL) should be in caps.

Get and Post parameters
-----------------------

* get and post params should be in camel case. 
