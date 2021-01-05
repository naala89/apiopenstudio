Settings
========

Copy ```settings.example.yml``` to ```example.yml```, and then edit the settings in ```example.yml```

- debug.handlers.*.level

    - Set to the debug verbosity that you require.

- debug.loggers.*.handlers

    - Add "console" to the array if you want console output.

- db

    - definition_path

        This is the path for the main DB definition, used in db creation. This should not be changed.

    - dbdriver

        The db driver, e.g. ```mysqli```.

    - host

        The DB hostname, e.g. ```localhost```.

    - root_password

        DB root password, used in ```install.php``` script.

    - username

        The DB user.

    - passwsord

        The DB password.

    - database

        The DB name.

    - options[debug]

        Turn on DB debug.
        
        See [ADOdb documentation](http://phplens.com/lens/adodb/docs-adodb.htm) for possible values.

    - charset

        The DB charset.

    - collation

        The DB collation.

- api

    - base_path

        The full server path to the docroot.

    - url

        The API url.

    - cache

        Set to true to enable caching on the server (opcode service automatically discovered), set to false to disable.
        
        If you have multiple services available on the server, you instruct Datagator to use a specific opcode by setting this value to ```apc``` for APC or ```memcache``` for MemCache.

    - default_format

        If the API does not receive an 'Accept' header, this is the format that it will return.
        
        See /includes/Output/ for the different possibilities.

    - token_life

        This is the time before a user's login token will expire and require logging in again.

    - core_account

        This is the account created on installation, for ApiOpenStudio's core resources. This should not be altered.

    - core_application

        This is the application created on installation, for ApiOpenStudio's core resources. This should not be altered.

    - core_resource_lock

        Setting this to false will allow you to edit core resources. Use with caution.

    - dir_public

        The relative path to the public directory from docroot.

    - dir_yaml

        The relative path to the resources directory from docroot.

    - dir_tmp

        The relative path to the tmp directory from docroot.

- admin

    - url

        The url for admin

    - pagination_step

        The number of entries on listing pages.

    - slim.displayErrorDetails

        Display Slim errors on the page. This should be set to false in production.

    - slim.determineRouteBeforeAppMiddleware.

        This should not be altered.

    - slim.debug

        Turn on debug in Slim. This should be set to false in production.

- twig

    - options.cache_enabled

        Turn on twig cache.

    - options.cache_path

        The relative path from docroot to the twig cache directory. This should not be altered.

    - options.debug

        Turn on twig debug mode. This should not be set to true on production.

    - template_path

        The relative path to admin templates from docroot. This should not be altered.

- email

    -   host
        
        The Email host.
        
    - username
    
        Authentication username
        
    - password
    
        Authentication password
    
    - from.email
    
        From address in the sent emails.
    
    - from.name
    
        From name in the sent emails.
