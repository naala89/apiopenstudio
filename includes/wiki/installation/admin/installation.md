Installing the codebase
=======================

### Composer

    composer create-projectapiopenstudio_admin:1.0.0-alpha

### Git

    git clone git@gitlab.com:apiopenstudio/apiopenstudio_admin.git

or

    git clone git@github.com:naala89/api_open_studio_admin.git

Settings
--------

    cp example.settings.yml settings.yml

Update the settings in ```.env```.
See [settings](/installation/admin/settings.html)

Server
------

1. Install [Composer][composer].
2. Install [npm][nodejs].
3. Set the file permissions:
    1. ```cd /path/to/api_open_studio```
    2. ```chown -R www-data:<my_group> ./*```
4. Run composer install in the docroot:
    1. ```composer install```
4. Run Gulp:
    1. ```gulp```
5. Update ```php.ini``` (if using non-apache server,
   see [Hardening your HTTP response headers][hardening_headers]):
    1. ```expose_php = Off```
6. Update ```httpd.conf```
    1. ```ServerSignature Off```
    2. ```ServerTokens Prod```

[hardening_headers]: https://scotthelme.co.uk/hardening-your-http-response-headers/#removingheaders

[nodejs]: https://nodejs.org/en/download/package-manager/

[composer]: https://getcomposer.org/
