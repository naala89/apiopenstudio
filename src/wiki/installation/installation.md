Requirements
============

* apache/nginx
* php >= 7.0
* mysql
* opcode (Memcache or APC)
* composer
* npm
* mcrpyt
* zip

Installing the codebase
========================

There are several ways to do this:

Clone the repository
--------------------

Where <my_group> is the group your server uses.

1. ```$ git clone git@gitlab.com:john89/gaterdata.git```
2. ```$ cd gaterdata```
3. ```$ chmod -R 760 .```
4. Create the settings file:
    1. ```$ cp config/settings.example.ini config/settings.ini```
    2. Update the values (See the config section for details).
    3. ```$ chmod config/settings.ini 600```

If you are using docker, you can skip the following steps.

5. ```$ chown -R www-data:<my_group> ./*```
6. Install [Composer](https://getcomposer.org/).
7. The following server modules are required:
   1. php-curl
   2. php-mbstring
   3. php-dom
   4. php-zip
8. Run composer install in the docroot:
    1. ```$ cd /path/to/gaterdata```
    2. ```$ composer install```
9. Run gulp:
    1. ```$ gulp all```
10. Create the wiki (optional)
    1. ```./vendor/bin/bookdown src/wiki/bookdown/josn```
11. Create an empty database and user. Give the user full permission for the DB.
    1. ``$ mysql -u root -p``
    2. ``$ CREATE DATABASE <db_name>;``
    3. ``$ GRANT ALL PRIVILEGES ON <db_name>.* TO <username>@localhost IDENTIFIED BY "<password>";``
12. Update ```php.ini``` (if using non-apache server, see [Hardening your HTTP response headers](https://scotthelme.co.uk/hardening-your-http-response-headers/#removingheaders)):
    1. ```expose_php = Off```
13. Update ```httpd.conf```
    1. ```ServerSignature Off```
    2. ```ServerTokens Prod```

Composer
--------

This is coming soon.
    
Production
----------

Remove the production non-critical files and directories:

1. ```$ cd gaterdata```
2. ```$ rm -R html/admin/install.php codeception.yml tests```