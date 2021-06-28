FROM debian:stretch

ARG DIRECTORY_INDEX
ARG DOCUMENT_ROOT

RUN apt-get autoclean
RUN apt-get update && apt-get install -y \
    apt-transport-https \
    ca-certificates \
    curl \
    software-properties-common \
    vim \
    gnupg \
    wget \
    ksh \
    unzip \
    zip \
    default-jdk \
    git

RUN wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
RUN echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php7.3.list

RUN apt-get update
RUN apt-get install -y apache2 \
    mysql-server \
    php7.1

RUN update-alternatives --set php /usr/bin/php7.1

RUN apt-get install -y php7.1-xml \
    php7.1-mbstring \
    php7.1-dev \
    php7.1-intl \
    php-pear \
    php7.1-curl\
    php7.1-mysql\
    php7.1-sqlite

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

## Make a lsb-release file so the installer knows the os (necessary for debian)
RUN cp /etc/os-release /etc/lsb-release

## Create and switch user for a non-root installation
RUN useradd -ms /bin/bash docker
USER docker

USER root

#Set php.ini configuration
RUN rm /etc/php/7.1/apache2/php.ini
COPY ./php/conf/apache/php.ini /etc/php/7.1/apache2/php.ini
COPY ./php/conf/cli/php.ini  /etc/php/7.1/cli/php.ini

# Set apache vhost
RUN echo ${DIRECTORY_INDEX}
RUN echo ${DOCUMENT_ROOT}
COPY ./apache/conf/vhost.conf /etc/apache2/sites-available
RUN sed -i -e "s|__DIRECTORY_INDEX__|${DIRECTORY_INDEX}|g" /etc/apache2/sites-available/vhost.conf
RUN sed -i -e "s|__DOCUMENT_ROOT__|${DOCUMENT_ROOT}|g" /etc/apache2/sites-available/vhost.conf
RUN ln -s /etc/apache2/sites-available/vhost.conf /etc/apache2/sites-enabled/vhost.conf
RUN rm /etc/apache2/sites-enabled/000-default.conf

RUN service mysql start && echo 'update user set plugin = "" where User = "root"; flush privileges;' | mysql -uroot mysql

COPY db/initial_db.sql  /tmp/initial_db.sql
RUN service mysql start && cat /tmp/initial_db.sql | mysql -uroot

#install tomcat
RUN wget https://archive.apache.org/dist/tomcat/tomcat-8/v8.5.51/bin/apache-tomcat-8.5.51.zip -P /tmp/
RUN mkdir /local/
RUN unzip /tmp/apache-tomcat-8.5.51.zip -d /tmp/
RUN mv /tmp/apache-tomcat-8.5.51 /local/tomcat
COPY ./tomcat/relis.war /local/tomcat/webapps/
RUN chmod -R +x /local/tomcat

#setup environment variables
RUN export JAVA_HOME=/usr/lib/jvm/default-java
RUN export JAVA_OPTS='-Djava.security.egd=file:///dev/urandom'

RUN export CATALINA_BASE=/local/tomcat
RUN export CATALINA_HOME=/local/tomcat
RUN export CATALINA_PID=/local/tomcat/temp/tomcat.pid
RUN export CATALINA_OPTS='-Xms512M -Xmx1024M -server -XX:+UseParallelGC'

RUN a2enmod rewrite
COPY ./entrypoint.sh /usr/local/bin
RUN sed 's/\r$//g' /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

WORKDIR ${DOCUMENT_ROOT}