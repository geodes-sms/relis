#!/bin/bash

chmod -R 777 /u/relis/public_html/cside/export_r /u/relis/public_html/cside/export_python
chmod -R 777 /u/relis/public_html/cside/sessions
chmod -R 777 /u/relis/public_html/relis_app/config

cd /u/relis/public_html/relis_app/config  && cp --no-clobber database.example.php database.php

cd /u/relis/public_html && composer install --no-dev --prefer-dist --optimize-autoloader

/usr/sbin/apache2ctl -D FOREGROUND

service apache2 restart