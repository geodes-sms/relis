#!/bin/bash

chmod -R 777 /u/relis/public_html/cside/sessions
chmod -R 777 /u/relis/public_html/relis_app/config
cd /u/relis/public_html/relis_app/config  && cp --no-clobber database.example.php database.php
sh /local/tomcat/bin/startup.sh
service mysql restart
/usr/sbin/apache2ctl -D FOREGROUND
service apache2 restart