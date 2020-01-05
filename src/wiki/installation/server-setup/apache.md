Apache
======

Admin
-----

    <VirtualHost *:80>
        ServerName admin.gaterdata.com
        ServerAdmin webmaster@gaterdata.com
        DocumentRoot /path/to/gaterdata/public/admin
        
        <Directory /path/to/gaterdata/public/admin>
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    
        ErrorLog ${APACHE_LOG_DIR}/admin.gaterdata.error.log
        CustomLog ${APACHE_LOG_DIR}/admin.gaterdata.access.log combined
    </VirtualHost>

Api
---

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

Wiki (optional)
---------------

    <VirtualHost *:80>
        ServerName wiki.gaterdata.com
        ServerAdmin webmaster@gaterdata.com
        DocumentRoot /path/to/gaterdata/public/wiki
        
        <Directory /path/to/gaterdata/public/wiki>
            AllowOverride All
            Order allow,deny
            Allow from all
        </Directory>
    
        ErrorLog ${APACHE_LOG_DIR}/wiki.gaterdata.error.log
        CustomLog ${APACHE_LOG_DIR}/wiki.gaterdata.access.log combined
    </VirtualHost>
