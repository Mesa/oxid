### Environment Variables ###

You can change some configuration to match your needs. Just set them in your
docker-compose.yml and add them to your environment block.

```
environment:
    MYSQL_HOST: oxid_db
    MYSQL_DATABASE: oxid
    MYSQL_USER: oxid
```

Default PHP Variables are named like their counterparts in php.ini

    PHP_ERROR_REPORTING "E_ERROR | E_WARNING | E_PARSE"
    PHP_MEMORY_LIMIT "256M"
    PHP_DATE_TIMEZONE "Europe/Berlin"
    PHP_DISPLAY_ERRORS "Off"
    PHP_UPLOAD_MAX_FILESIZE  "8m"


Docker VHost configuration variables

    DOCKER_DOCUMENT_ROOT "/data/"
    DOCKER_SERVER_ADMIN "admin@localhost"
    DOCKER_ERROR_LOG "/dev/stdout"
    DOCKER_CUSTOM_LOG "/dev/stdout combined"
    DOCKER_ALLOW_OVERRIDE "All"


Apache2 default configuration values

    APACHE_RUN_USER "www-data"
    APACHE_RUN_GROUP "www-data"
    APACHE_LOG_DIR "/dev/stdout"
    APACHE_LOCK_DIR "/var/lock/apache"
    APACHE_PID_FILE "/tmp/apache2.pid"
    APACHE_SERVERNAME "localhost"


Oxid admin user credentials

    OXID_ADMIN_PASSWORD "docker"
    OXID_ADMIN_USERNAME "docker"


OXID configuration from  config.inc.php

    OXID_SHOP_URL "http://localhost"
    OXID_UTF_MODE 1
    OXID_IDEBUG 0
    OXID_COMPILE_DIR "/tmp"


OXID database configuration, equal names for mariaDB/mysql container variables.
You have to define them for both webserver image and database image

    MYSQL_HOST "oxid_db"
    MYSQL_USER "oxid"
    MYSQL_PASSWORD "oxid"
    MYSQL_DATABASE "oxid"
