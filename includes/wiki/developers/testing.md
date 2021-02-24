Testing
=======

Testing is done with [Codeception](http://codeception.com/).
From Docroot, run:

```$ vendor/bin/codecept -v```

If you are running testcase first time in api suite, then in your api directory you will not have api tester file. You need to generate that so run following command:

```$ vendor/bin/codecept build```

Config
------

Domains for testing in environments are stored in ```/tests/api.suite.yml```

Testing user
------------

The following testing credentials  are stored in /tests/_support/Helper/api.php

* Account: Datagator
* Application: Testing
* Username: tester
* Password: tester_pass

Creating Tests
--------------

```$ vendor/bin/codecept generate:cept TestName```

Create api test
---------------

```$ vendor/bin/codecept generate:cept api TestName```

Test YAML files
---------------

These are in ```/tests/_data/```

Running tests
-------------

```$ vendor/bin/codecept run --env staging|local|prod```

```$ vendor/bin/codecept run --env staging|local|prod api```

```$ vendor/bin/codecept run --env staging|local|prod api testName```

If only testing API locally:

```$ vendor/bin/codecept run api```

```$ vendor/bin/codecept run api testName```
