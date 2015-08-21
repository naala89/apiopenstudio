Datagator
=========

The API project

Install
=======
1. $git clone gitolite@naala.com.au:datagator
2. install composer (see https://getcomposer.org/).
3. $composer install
4. create a database.
5. update Config.php:
  1. set server region in the the $_server array (development, staging or production) under the index of the server name that your server uses.
  2. set database credentials in the region function you defined in $_server.
  3. set other desired values in the region function you defined in $_server.
6. run includes/scripts/db/structure.sql.
7. run includes/scripts/db/data.sql.

Requirements
============
1. php >= 5.3
2. mcrpyt
3. composer