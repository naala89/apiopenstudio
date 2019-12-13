[[_TOC_]]

There are several methods to install GaterData:

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
9. Create an empty database and user. Give the user full permission for the DB.
    1. ``$ mysql -u root -p``
    2. ``$ CREATE DATABASE <db_name>;``
    3. ``$ GRANT ALL PRIVILEGES ON <db_name>.* TO <username>@localhost IDENTIFIED BY "<password>";``
10. Update ```php.ini``` (if using non-apache server, see [Hardening your HTTP response headers](https://scotthelme.co.uk/hardening-your-http-response-headers/#removingheaders)):
    1. ```expose_php = Off```
11. Update ```httpd.conf```
    1. ```ServerSignature Off```
    2. ```ServerTokens Prod```

### Server setup

#### Apache

##### Admin

    <VirtualHost *:80>
        ServerName admin.gaterdata.com
        ServerAdmin webmaster@gaterdata.com
        DocumentRoot /path/to/gaterdata/html/admin
        
        <Directory /path/to/gaterdata/html/admin>
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    
        ErrorLog ${APACHE_LOG_DIR}/admin.gaterdata.error.log
        CustomLog ${APACHE_LOG_DIR}/admin.gaterdata.access.log combined
    </VirtualHost>

##### Api

    <VirtualHost *:80>
        ServerName gaterdata.com
        ServerAdmin webmaster@localhost
        DocumentRoot /path/to/gaterdata
        
        <Directory /path/to/gaterdata>
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
        
        ErrorLog ${APACHE_LOG_DIR}/gaterdata.error.log
        CustomLog ${APACHE_LOG_DIR}/gaterdata.access.log combined
    </VirtualHost>

#### Nginx

##### Admin

    server {
        server_name admin.gaterdata.com;

        listen [::]:443 ssl;
        listen 443 ssl;

        root /path/to/gaterdata/html/admin;
        index index.php;

        access_log /var/log/nginx/access.admin.gaterdata.com.log;
        error_log /var/log/nginx/error.admin.gaterdata.com.log;

        location / {
            try_files $uri /index.php$is_args$args;
        }

        location ~ \.php$ {
            #NOTE: You should have "cgi.fix_pathinfo = 0;" in php.ini
            include fastcgi.conf;
            fastcgi_intercept_errors on;
            fastcgi_pass php;
        }

        location ~ /\.ht {
            deny  all;
        }

        ssl_certificate /path/to/admin.gaterdata.com/fullchain.pem;
        ssl_certificate_key /path/to/admin.gaterdata.com/privkey.pem; 
    }

    server {
        server_name admin.gaterdata.com;

        listen 80;
        listen [::]:80;

        if ($host = admin.gaterdata.com) {
            return 301 https://$host$request_uri;
        }

        return 404;
    }

##### API

Composer
--------

This is coming soon
    
Production
----------

Remove the production non-critical files and directories:

1. ```$ cd gaterdata```
2. ```$ rm -R html/admin/install.php ./*.md codeception.yml resources tests```