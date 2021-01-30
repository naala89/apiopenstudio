Installing the codebase
=======================

### Composer

    composer create-project apiopenstudio/apiopenstudio:1.0.0-beta

### Git

    git clone git@gitlab.com:john89/apiopenstudio.git

Settings
--------

    cp example.env .env
    cp example.settings.yml settings.yml

Update the values as you wish.

Server
------

If you are using docker, you can skip the following steps.

5. ```chown -R www-data:<my_group> ./*```
6. Install [Composer](https://getcomposer.org/).
7. The following server modules are required:
   1. php-curl
   2. php-mbstring
   3. php-dom
   4. php-zip
8. Run composer install in the docroot:
    1. ```cd /path/to/apiopenstudio```
    2. ```composer install```
9. Install npm
10. Run npm install in the docroot:
    1. ```npm install```
11. Run gulp:
    1. ```gulp all```
12. Create the wiki (optional)
    1. ```./vendor/bin/bookdown src/wiki/bookdown/json```
13. Create an empty database and user. Give the user full permission for the DB.
    1. ``mysql -u root -p``
    2. ``CREATE DATABASE <db_name>;``
    3. ``GRANT ALL PRIVILEGES ON <db_name>.* TO <username>@localhost IDENTIFIED BY "<password>";``
14. Update ```php.ini``` (if using non-apache server, see [Hardening your HTTP response headers](https://scotthelme.co.uk/hardening-your-http-response-headers/#removingheaders)):
    1. ```expose_php = Off```
14. Update ```httpd.conf```
    1. ```ServerSignature Off```
    2. ```ServerTokens Prod```
    
Production
----------

Remove the production non-critical files and directories:

1. ```cd apiopenstudio```
2. ```rm -R public/admin/install.php codeception.yml tests```
