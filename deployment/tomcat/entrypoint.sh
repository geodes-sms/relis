#!/bin/bash
ls -l /usr/local/tomcat/bin
chmod +x /usr/local/tomcat/bin/catalina.sh
/usr/local/tomcat/bin/catalina.sh run & python3 /app.py && fg