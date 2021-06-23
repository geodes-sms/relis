# ReLiS

*ReLiS is a tool for conducting systematic reviews.*

Systematic review is a technique used to search for evidence in scientific literature that is conducted in a formal manner, following a well-defined process, according to a previously elaborated protocol. Conducting a systematic reviews involves many steps over a long period of time, and is often laborious and repetitive. This is why we have created ReLiS which provides essential software support to reviewers in conducting high quality systematic reviews. With ReLiS, you can plan, conduct, and report your review. 

Unlike other systematic review tools, ReLiS is an online tool that automatically installs and configures your projects. You conduct reviews collaboratively and iteratively on the cloud. ReLiS is engineered following a model-driven development approach. It features a domain-specific modeling editor and an architecture that enables on-the-fly installation and (re)configuration of multiple concurrently running projects.

You can use a publically available instance of ReLiS at [http://relis.iro.umontreal.ca/](http://relis.iro.umontreal.ca/). This GitHub repository allows you to install ReLiS on your servers.

## Features
High-level features supported:
- Collaboration support
- Protocol development and modification iteratively
- Traceability
- Decision tracking
- Support inclusion and exculsion
- Support quality assessment
- Data extraction form
- Data management
- Data maintenance
- Basic statistical analysis
- Report preparation
- Data sharing
- Visualization
- Export studies and data
- Storage of studies (all but PDF)

# Installation
The project can be installed locally using docker. Make sure to follow the pre-requisite steps to have an up and running docker environment.

### Pre-requisite:
- Install docker on your local environment (download at: https://www.docker.com/products/docker-desktop)
- Install docker-compose on your local environment (it comes by default with docker on mac and windows)
- Run command "docker-compose" to check that docker is correctly installed
- For Windows users, if Docker cannot start with the following error : 
  "Hardware assisted virtualization and data execution protection must be enabled in the BIOS" then excute following steps:
  - Enable features : Virtual Machine Platform, Windows Subsystem for Linux and Hyper-V using "Windows Features"
    (See https://docs.docker.com/docker-for-windows/troubleshoot/#virtualization for more information)
  - Restart you system and press esc key or any correspondent key to enter the BIOS settings in windows 
  - Press f10 or other key to enter BIOS Setup
  - Go to System Configuration then Device Configuration 
  - Enable Virtualization 
  - Restart your aystem
  If Docker still not start with the error "Docker.ApiServices.WSL2.WslKernet NotInstalledException" then excute following steps:
  - use the link https://docs.microsoft.com/en-us/windows/wsl/install-win10#step-4---download-the-linux-kernel-update-package to download and intall linux kernal update 
  - Restart you system if Docker not start 

### Build and run the application:
1. Clone the project from GitHub (git clone https://github.com/geodes-sms/relis.git)
2. Run command "docker-compose build" from the directory **relis_deployment/**
3. Run command "docker-compose up -d" from the directory **relis_deployment/**
(For **windows users** , if you get the error ``relis-application-service | standard_init_linux.go:211: exec user process caused "no such file or directory"``: check if the file ``relis_deployment/docker/entrypoint.sh`` EOL is not CRLF;  if it is, convert it from CRLF to LF and go back to step 2.)
4. From your browser go to url **localhost:8083** to access the application, default credentials  are admin::123.
5. For test purpose you can also access the database using PHPMyAdmin on **localhost:8083/phpmyadmin**, the user is root, no password required (You should use this feature only on the local environment). If you get the error "Incorrect permissions on the configuration file, it should not be write to everyone!" then add the statement $cfg['CheckConfigurationPermissions'] = false; to the phpmyadmin config file: relis-docker-implementation\phpmyadmin\config.inc.php

### Usefull command
To connect inside your docker container run the command "docker-compose exec relis-application-service bash" from the directory **relis_deployment/**


# Distribution


The source code is licensed under a [GNU GENERAL PUBLIC LICENSE 3](https://www.gnu.org/copyleft/gpl.html) ![GNU GPL v3](https://img.shields.io/badge/license-GPLv3-blue.svg)

# Change log
## Version 1.0
#### DATE
- 
