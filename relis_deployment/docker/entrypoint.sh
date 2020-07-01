#!/bin/bash

chmod -R 777 /u/relis/public_html/cside/sessions
sh /local/tomcat/bin/startup.sh
service mysql restart
/usr/sbin/apache2ctl -D FOREGROUND
service apache2 restart