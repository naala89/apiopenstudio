Apache
======

Admin virtualhost
-----------------

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
