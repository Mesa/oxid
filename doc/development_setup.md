Development Setup
=================

For development purposes you can start all container and mount the src folder to all of them.


Create your `docker-composer.yml` and past the following code:

[docker-compose.yml - RAW](https://raw.githubusercontent.com/Mesa/oxid/master/docker-compose.yml)
```yaml
version: '3'

volumes:
  oxid_db:

services:
    php5.6:
      image: mesa/oxid:php5.6
      restart: always
      container_name: oxid_php5.6
      ports:
        - "8082:80"
      volumes:
        - ./src/:/data/
      links:
        - oxid_db
      environment:
        MYSQL_HOST: oxid_db
        MYSQL_DATABASE: oxid
        MYSQL_USER: oxid
        MYSQL_PASSWORD: oxid
        OXID_SHOP_URL: "http://localhost:8082"
        OXID_IDEBUG: 1
        OXID_ADMIN_PASSWORD: oxid
        OXID_ADMIN_USERNAME: oxid

    php7.0:
      image: mesa/oxid:php7.0
      restart: always
      container_name: oxid_php7.0
      ports:
        - "8081:80"
      volumes:
        - ./src/:/data/
      links:
        - oxid_db
      environment:
        MYSQL_HOST: oxid_db
        MYSQL_DATABASE: oxid
        MYSQL_USER: oxid
        MYSQL_PASSWORD: oxid
        OXID_SHOP_URL: "http://localhost:8081"
        OXID_IDEBUG: 1
        OXID_ADMIN_PASSWORD: oxid
        OXID_ADMIN_USERNAME: oxid
        PHP_ERROR_REPORTING: -1

    oxid_db:
      image: mariadb:latest
      restart: always
      container_name: oxid_db
      ports:
        - "3306:3306"
      volumes:
        - oxid_db:/var/lib/mysql
    # Mount your DB dumps here
    #   - ./db-dumps/:/docker-entrypoint-initdb.d/
      environment:
        MYSQL_ROOT_PASSWORD: root
        MYSQL_DATABASE: oxid
        MYSQL_USER: oxid
        MYSQL_PASSWORD: oxid

```

Now everything is prepared for docker, you can start up all containers by executing

```bash
docker-compose up -d
```

The startup process will take some time, be patient and wait a minute.

## Install OXID
With the last update I added the posibility to install OXID eshop from Github through a bash script.

Just execute ```/etc/install_oxid.sh``` inside your docker container. For example with:

    docker-compose exec php5.6 /etc/install_oxid.sh v4.10.5

You can specifiy a OXID version number to install or leave the parameter empty. The optional parameter will default to
the latest OXID version (currently v4.10.5).

OXID Version numbers are branch names or tag names from github, like:

* v4.9.10
* v4.10.5
* v6.0.0-rc2


## Setup
Now you can setup your Project by visiting [http://localhost:8082/setup/](http://localhost:8082) and follow the
installation steps.

Remember the values you defined in your `docker-compose.yml`. When you are asked for the Database host, you enter `oxid_db`
because this is the id and domain name you defined by "linking" the container with `oxid_db`

    php5.6:
    ...
      links:
        - oxid_db

The default values for MySQL Database, username and password are `oxid`.

## Import DB
You want to import your own database dump? No problem, you can access the Database with your own Tools, the port `3306`
is external accessible:

    oxid_db:
    ...
      ports:
        - "3306:3306"

or you can run an manual import you database dump or get the demo dump from [here](https://raw.githubusercontent.com/Mesa/oxid/master/demo/db-dumps/oxid.sql)

    curl -OL https://raw.githubusercontent.com/Mesa/oxid/master/demo/db-dumps/oxid.sql

to load the sql from github and save it on oxid.sql. Now you can pass the data to you local mariadb and execute the
SQL from you host machine on the Database container by executing:

    docker-compose exec oxid_db mysql -u root -p < oxid.sql

This also applies to all other sql you want to execute, just omitt


## Reset password

When you lost your Admin password you can reset it to you environment variables `OXID_ADMIN_PASSWORD` and `OXID_ADMIN_USERNAME`
with the new script.

    docker-compose exec php5.6 /etc/reset_admin.sh

or

    docker-compose exec php7.0 /etc/reset_admin.sh

The variable can be defined in your docker-compose.yml.

Or you can do it by hand with this script

    mysql -h ${MYSQL_HOST} -u ${MYSQL_USER} -p${MYSQL_PASSWORD} -e "
        UPDATE oxuser
        SET OXPASSWORD = MD5( CONCAT( '${OXID_ADMIN_PASSWORD}',  '${NEW_SALT}' ) ) ,
            OXPASSSALT = hex('${NEW_SALT}'),
            OXUSERNAME = '${OXID_ADMIN_USERNAME}'
        where oxid = 'oxdefaultadmin';" ${MYSQL_DATABASE}

You have to replace all environment variables with your new values.