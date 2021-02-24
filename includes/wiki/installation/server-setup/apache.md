Apache
======

Admin
-----

    <VirtualHost *:80>
        ServerName admin.apiopenstudio.com
        ServerAdmin webmaster@apiopenstudio.com
        DocumentRoot /path/to/apiopenstudio/public/admin
        
        <Directory /path/to/apiopenstudio/public/admin>
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    
        ErrorLog ${APACHE_LOG_DIR}/admin.apiopenstudio.error.log
        CustomLog ${APACHE_LOG_DIR}/admin.apiopenstudio.access.log combined
    </VirtualHost>

Api
---

    <VirtualHost *:80>
        ServerName apiopenstudio.com
        ServerAdmin webmaster@localhost
        DocumentRoot /path/to/apiopenstudio
        
        <Directory /path/to/apiopenstudio>
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
        
        ErrorLog ${APACHE_LOG_DIR}/apiopenstudio.error.log
        CustomLog ${APACHE_LOG_DIR}/apiopenstudio.access.log combined
    </VirtualHost>

Wiki (optional)
---------------

    <VirtualHost *:80>
        ServerName wiki.apiopenstudio.com
        ServerAdmin webmaster@apiopenstudio.com
        DocumentRoot /path/to/apiopenstudio/public/wiki
        
        <Directory /path/to/apiopenstudio/public/wiki>
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    
        ErrorLog ${APACHE_LOG_DIR}/wiki.apiopenstudio.error.log
        CustomLog ${APACHE_LOG_DIR}/wiki.apiopenstudio.access.log combined
    </VirtualHost>
