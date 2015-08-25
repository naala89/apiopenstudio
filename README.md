Datagator
=========
The API project

Install
=======
i. $git clone gitolite@naala.com.au:datagator
i. install [Composer](https://getcomposer.org/).
i. $composer install
i. create a database.
i. update Config.php:
  i. set server region in the the $_server array (development, staging or production) under the index of the server name that your server uses.
  i. set database credentials in the region function you defined in $_server.
  i. set other desired values in the region function you defined in $_server.
i. run includes/scripts/db/structure.sql.
i. run includes/scripts/db/data.sql.

Requirements
============
*. php >= 5.3
*. mcrpyt
*. composer
*. mysql