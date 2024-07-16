#!/bin/bash

chown -R www-data:www-data /u/relis/public_html/cside/export_r /u/relis/public_html/cside/export_python
chown -R www-data:www-data /u/relis/public_html/cside/sessions
chown -R www-data:www-data /u/relis/public_html/relis_app/

chmod -R 664 /u/relis/public_html/cside/export_r /u/relis/public_html/cside/export_python
chmod -R 664 /u/relis/public_html/cside/sessions
chmod -R 664 /u/relis/public_html/relis_app/

cd /u/relis/public_html/relis_app/config  && cp --no-clobber database.example.php database.php

cd /u/relis/public_html && composer install --no-dev --prefer-dist --optimize-autoloader

/usr/sbin/apache2ctl -D FOREGROUND

service apache2 restart
