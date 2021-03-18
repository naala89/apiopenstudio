Settings
========

Copy ```settings.example.yml``` to ```example.yml```, and then edit the settings
in ```example.yml```

* debug.handlers.*.level
    * Set to the debug verbosity that you require.
* debug.loggers.*.handlers
    * Add "console" to the array if you want console output.
* db
    * definition_path
        * This is the path for the main DB definition, used in db creation. This
          should not be changed.
    * dbdriver
        * The db driver, e.g. ```mysqli```.
    * host
        * The DB hostname, e.g. ```localhost```.
    * root_password
        * DB root password, used in ```install.php``` script.
    * username
        * The DB user.
    * password
        * The DB password.
    * database
        * The DB name.
    * options[debug]
        * Turn on DB debug.
        * See [ADOdb documentation][adodb_documentation] for possible values.
    * charset
        * The DB charset.
    * collation
        * The DB collation.
* api
    * base_path
        * The full server path to the docroot.
    * url
        * The API url.
    * cache
        * Set to true to enable caching on the server (opcode service
          automatically discovered), set to false to disable.
        * If you have multiple services available on the server, you instruct
          Datagator to use a specific opcode by setting this value to ```apc```
          for APC or ```memcache``` for MemCache.
    * default_format
        * If the API does not receive an 'Accept' header, this is the format
          that it will return.
        * See /includes/Output/ for the different possibilities.
    * token_life
        * This is the time before a user's login token will expire and require
          logging in again.
    * core_account
        * This is the account created on installation, for ApiOpenStudio's core
          resources. This should not be altered.
    * core_application
        * This is the application created on installation, for ApiOpenStudio's
          core resources. This should not be altered.
    * core_resource_lock
        * Setting this to false will allow you to edit core resources. Use with
          caution.
    * dir_public
        * The relative path to the public directory from docroot.
    * dir_yaml
        * The relative path to the resources directory from docroot.
    * dir_tmp
        * The full path to the tmp directory on the server.
* email
    * host
        * The Email host.
    * username
        * Authentication username
    * password
        * Authentication password
    * from.email
        * From address in the sent emails.
    * from.name
        * From name in the sent emails.

[adodb_documentation]: https://adodb.org/dokuwiki/doku.php?id=project:documentation
