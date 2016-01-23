Datagator
=========
The API project

Install
-------
1. $git clone gitolite@naala.com.au:datagator
2. install [Composer](https://getcomposer.org/).
3. $composer install
4. create a database.
5. update Config.php:
  1. set server region in the the $_server array (development, staging or production) under the index of the server name that your server uses.
  2. set database credentials in the region function you defined in $_server.
  3. set other desired values in the region function you defined in $_server.
6. run includes/scripts/db/structure.sql.
7. run includes/scripts/db/data.sql.

Requirements
------------
* apache
* https
* php >= 5.3
* mysql
* opcode (Memcache or APC)
* composer
* mcrpyt

Error codes
-----------
    0 - Core error
    1 - Processor format error
    2 - DB error
    3 - Invalid API call
    4 - Authorisation error
    5 - External error
    6 - Invalid processor input
    7 - Invalid application
