Gaterdata
=========

Contents
========

* [Installation](https://gitlab.com/john89/gaterdata/wikis/Installatiion)
* [Docker](https://gitlab.com/john89/gaterdata/wikis/Docker)

Requirements
------------

* apache/nginx (see [foobar](https://foobar) for nginx config file)
* php >= 7.0
* mysql
* opcode (Memcache or APC)
* composer
* mcrpyt
* zip


User Roles and permissions
--------------------------

### Administrator

* Create accounts
* Edit accounts
* Delete accounts
* Invite users
* Enable, disable or delete users
* Assign user roles

### Account manager

* Create applications
* Edit applications
* Delete applications
* Invite users
* Disable or delete users
* Assign/Revoke user roles to their account

### Application manager

* Invite users
* Disable or delete users
* Assign/Revoke user roles to their account

### Developer

* Create resources for their account/applications
* Delete resources for their account/applications
* Edit resources for their account/applications

### Application manager

* Invite users
* Disable or delete users
* Assign user roles

Styling Admin
-------------

Install npm
    https://www.npmjs.com/get-npm

Ensure npm and gulp are up to date.

    npm i -g npm
    npm install gulp

Install the node dependencies.

    cd gaterdata
    npm install
    
Edit and Compile.

    gulp {watch,js,css,img}

The gulpfile.js includes compilation of sass and minification of js and css
files.

You can add your own css to ```/src/css/main.css```.



https://github.com/stucki/docker-lemp
https://stackoverflow.com/questions/34875581/get-composer-php-dependency-manager-to-run-on-a-docker-image-build/42147748#42147748
https://stackoverflow.com/questions/52400227/how-to-connect-php-and-composer-image-using-docker-composer
https://medium.com/the-code-review/top-10-docker-commands-you-cant-live-without-54fb6377f481
    kill all running containers with docker kill $(docker ps -q)
    delete all stopped containers with docker rm $(docker ps -a -q)
    delete all images with docker rmi $(docker images -q)
    List Containers docker ps

https://codar.club/blogs/docker-compose-builds-nginx-php-mysql.
https://www.linuxnix.com/what-is-data-persistence-and-how-can-we-use-it-via-docker/
https://blog.ssdnodes.com/blog/host-multiple-websites-docker-nginx/