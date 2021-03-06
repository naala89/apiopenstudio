Apache
======

Api virtualhost
---------------

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
