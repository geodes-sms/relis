
# Dev Environment Setup

Latest Development Environment with microservices architecture and updated components.

>>>
 Changelog:
1. Split the monolithic application to microservices architecture majorly in these categories
    - db
    - Tomcat
    - Phpmyadmin
    - relis-application
2. Xdebug defaulted to port 9000
3. Phpmyadmin, Mariadb and tomcat are defaulted to the latest release as per their official release decreasing the management of these packages from our side.
4. Php-7.1 and apache are using the Debian Buster as streach is EOL since June 2022.
>>>



## Deployment

To deploy this project run

```bash
cd relis_dev
docker-compose up --build -d
```
Major points to notice:
1. Make sure to open port `9000` from your firewall on the host side, specially for `Ubunut` and other linux distributions. To do this in Ubunut use this command `sudo ufw allow 9000`.
2. Current Debug key is set to `VSCODE` which is defalut if working in VSCode. You can change that [here](https://github.com/gauranshkumar/relis/blob/a60fca489288b30c5b02208528977d84f50a4446/relis_dev/docker/php/conf/50_xdebug.ini#L10).
3. Filesystem Permission issue can occur depending on the user, to fix this change the file permission to `777` **only in Dev Environment**. Use command `sudo chmod -R 777 relis/`

## Project Tree

```
relis_dev
├── docker
│   ├── apache
│   │   └── conf
│   │       └── vhost.conf
│   ├── conf
│   │   └── vhost.conf
│   ├── db
│   │   ├── config.inc.php
│   │   └── initial_db.sql
│   ├── Dockerfile
│   ├── entrypoint.sh
│   ├── php
│   │   └── conf
│   │       ├── 50_xdebug.ini
│   │       ├── apache
│   │       │   └── php.ini
│   │       ├── cli
│   │       │   └── php.ini
│   │       └── php.ini
│   ├── README.md
│   └── tomcat
│       └── relis.war
└── docker-compose.yml
```
## Authors

- [@gauranshkumar](https://www.github.com/gauranshkumar)

