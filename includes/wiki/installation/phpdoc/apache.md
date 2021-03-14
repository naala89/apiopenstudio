Apache (optional)
=================

PHPDoc (optional)
-----------------

    <VirtualHost *:80>
        ServerName phpdoc.apiopenstudio.com
        ServerAdmin webmaster@apiopenstudio.com
        DocumentRoot /path/to/apiopenstudio/public/phpdoc
        
        <Directory /path/to/apiopenstudio/public/phpdoc>
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    
        ErrorLog ${APACHE_LOG_DIR}/phpdoc.apiopenstudio.error.log
        CustomLog ${APACHE_LOG_DIR}/phpdoc.apiopenstudio.access.log combined
    </VirtualHost>
