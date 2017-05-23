#!/usr/bin/env php
<?php ob_start(); include __DIR__ . "/config.php"; ?>

Oxid Docker
===========

This is an unofficial Docker image for OXID eShop Community Edition.
This images is based on Ubuntu <?= UBUNTU_VERSION ?>, Apache2, PHP 5.6 and the current OXID CE version <?= OXID_VERSION ?>


#### Requirements ####
You need [docker-compose](https://docs.docker.com/compose/) to create your container and use
the docker-compose.yml file.


## Getting started ##
Copy all files from [demo](https://github.com/Mesa/oxid/tree/master/demo) sub folder and run
```bash
docker-compose up
```


Create a docker-compose.yml with following content:

```yaml
<?php include __DIR__ . "/../../demo/docker-compose.yml"; ?>
```
Create a folder named `db-dumps` and put the [demo/db-dumps/oxid.sql](demo/db-dumps/oxid.sql) in there.
During the first image run, the sql dump will be imported, with an existing database (after the first container run)
the sql dump file will be ignored.

Execute:

```bash
docker-compose up
```
and visit [localhost](http://localhost). There is no need to run execute the oxid setup, just use the demo dump sql.

Dont forget to change your admin user name and password in your docker-compose.yml, the default values are "oxid".
Only idiots don't change default passwords and you are no idiot, aren't you.

```
OXID_ADMIN_PASSWORD: oxid
OXID_ADMIN_USERNAME: oxid
```


### Backup ###

Change database name, user and password to your config values.
```bash
docker exec -i oxid_db mysqldump oxid -u oxid -poxid > oxid.sql
```


### Environment Variables ###

Default PHP Variables named like their counterparts in php.ini
```
PHP_ERROR_REPORTING "E_ERROR | E_WARNING | E_PARSE"
PHP_MEMORY_LIMIT "256M"
PHP_DATE_TIMEZONE "Europe/Berlin"
PHP_DISPLAY_ERRORS "Off"
PHP_UPLOAD_MAX_FILESIZE  "8m"
```


This docker v-host configuration variables
```
DOCKER_DOCUMENT_ROOT "/data/"
DOCKER_SERVER_ADMIN "admin@localhost"
DOCKER_ERROR_LOG "/dev/stdout"
DOCKER_CUSTOM_LOG "/dev/stdout combined"
DOCKER_ALLOW_OVERRIDE "All"
```

Apache2 default configuration values
```
APACHE_RUN_USER "www-data"
APACHE_RUN_GROUP "www-data"
APACHE_LOG_DIR "/dev/stdout"
APACHE_LOCK_DIR "/var/lock/apache"
APACHE_PID_FILE "/tmp/apache2.pid"
APACHE_SERVERNAME "localhost"
```

Oxid admin user credentials
```
OXID_ADMIN_PASSWORD "docker"
OXID_ADMIN_USERNAME "docker"
```

OXID configuration from  config.inc.php
```
OXID_SHOP_URL "http://localhost"
OXID_UTF_MODE 1
OXID_IDEBUG 0
OXID_COMPILE_DIR "/tmp"
```

OXID database configuration, equal names for mariaDB/mysql container variables.
You have to define them for both webserver image and database image
```
MYSQL_HOST "oxid_db"
MYSQL_USER "oxid"
MYSQL_PASSWORD "oxid"
MYSQL_DATABASE "oxid"

```


### Versions: ###
* OXID eShop Community Edition. <?= OXID_VERSION ?> 
* Apache/<?= APACHE_VERSION ?> (Ubuntu)
* Ubuntu <?= UBUNTU_VERSION ?> 
* PHP <?= PHPVERSION ?>


##### License #####
The MIT License (MIT)

<?php
$content = ob_get_clean();
file_put_contents(__DIR__ . "/../../README.md", $content);
