FROM tomcat:8.5.51-jdk8-openjdk-slim

RUN apt-get update && apt-get install -y \
    python3 \
    python3-pip \
    && rm -rf /var/lib/apt/lists/*
RUN pip3 install --upgrade pip
RUN pip3 install fastapi uvicorn
COPY app.py /app.py
COPY ./relis.war /usr/local/tomcat/webapps/relis.war
COPY ./entrypoint.sh /usr/local/bin
COPY ./testfiles/classification_install_demoTestProject.php /u/relis/public_html/workspace/dslforge_workspace/tests/src-gen/classification_install_demoTestProject.php

COPY ./testfiles/classification_install_demoTestProject.pdf /u/relis/public_html/workspace/dslforge_workspace/tests/src-gen/classification_install_demoTestProject.pdf

COPY ./testfiles/classification_install_demoTestProject2.php /u/relis/public_html/workspace/dslforge_workspace/tests/src-gen/classification_install_demoTestProject2.php


RUN sed 's/\r$//g' /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
EXPOSE 8080 
EXPOSE 8181
ENTRYPOINT [ "/usr/local/bin/entrypoint.sh" ]