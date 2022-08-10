Installing the codebase
=======================

### Composer

    composer create-project apiopenstudio/apiopenstudio:1.0.0-alpha

### Git

    git clone git@gitlab.com:apiopenstudio/apiopenstudio.git

or

    git clone git@github.com:naala89/apiopenstudio.git

Settings
--------

    cp example.settings.yml settings.yml

Update the settings in `.env`. See [settings][settings]

Server
------

1. Install [Composer][composer]
2. The following PHP extensions are required:
    1. `php-curl`
    2. `php-mbstring`
    3. `php-dom`
    4. `php-zip`
3. Set the file permissions:
    1. `cd /path/to/apiopenstudio`
    2. `chown -R www-data:<my_group> ./*`
4. Run composer install in the docroot:
    1. `composer install`
5. Set up the database.
    1. `./vendor/bin/install`
6. Update `php.ini` (if using non-apache server,
   see [Hardening your HTTP response headers][hardening_headers]):
    1. `expose_php = Off`
7. Update `httpd.conf`
    1. `ServerSignature Off`
    2. `ServerTokens Prod`

Links
-----

* [Hardening headers][hardening_headers]
* [Composer][composer]
* [ApiOpenStudio settings][settings]

[hardening_headers]: https://scotthelme.co.uk/hardening-your-http-response-headers/#removingheaders

[composer]: https://getcomposer.org/

[settings]: /installation/api/settings.html
