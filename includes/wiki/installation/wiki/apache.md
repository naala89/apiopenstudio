Apache (optional)
=================

Wiki virtualhost 
----------------

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
