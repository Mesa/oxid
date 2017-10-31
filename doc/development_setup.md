Development Setup
=================

For development purposes you can start all container at the same time and mount the src folder to each of them.
Every container binds to a different port on your host machine.

Create your `docker-composer.yml` and past the following code:

```yaml
version: '3'

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
        OXID_IDEBUG: 0
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
        OXID_IDEBUG: 0
        OXID_ADMIN_PASSWORD: oxid
        OXID_ADMIN_USERNAME: oxid
        PHP_ERROR_REPORTING: -1

    oxid_db:
      image: mesa/oxid:db
      restart: always
      container_name: oxid_db
      ports:
        - "3306:3306"
      volumes:
# Presist your data when needed.
#       - ./oxid_db:/var/lib/mysql
    # Mount your DB dumps here
        - ./db-dumps/:/docker-entrypoint-initdb.d/
      environment:
        MYSQL_ROOT_PASSWORD: root
        MYSQL_DATABASE: oxid
        MYSQL_USER: oxid
        MYSQL_PASSWORD: oxid

```
[docker-compose.yml - (RAW)](https://raw.githubusercontent.com/Mesa/oxid/master/docker-compose.yml)

Now everything is prepared for docker, you can start up all containers by executing:

```bash
docker-compose up -d
```

The container initialisation will take some time, be patient and wait a minute.

You are started the prepared containers containing Apache2, PHP and composer, but there is no OXID CE source
code preinstalled. You have to "install/download" OXID or put your existing project files under ```src/source```.

## Install OXID
When you start with a new install of OXID or want to test a version diffrent from your current, you can install OXID CE
through a script installed in each container.

Just execute ```/etc/install_oxid.sh``` inside your docker container.

    docker-compose exec [docker-container-name] /etc/install_oxid.sh -v [oxid-version]

for example:

    docker-compose exec php5.6 /etc/install_oxid.sh -v v4.10.5


You can specifiy a OXID version number to install or leave the parameter empty. The optional parameter will default to
the latest OXID version (currently v4.10.5).


OXID Version numbers are branch names or tag names from github, like:

* v4.9.10
* v4.10.5
* v6.0.0-rc2

but note the leading char "v". This is required and its to easy to forget.

The option ```-f``` will delete your ```config.inc.php``` and create a new one, with environment variables injected.
This is required, because every container need to set individial values to you shop.

The option ```--db``` will drop your schema/database and create a new one, with utf8 enabled. This will prevent some nasty
Database related OXID bug, like mixed collations.

You can skip the next block, you have already downloaded OXID source files.

## Copy existing project
To start with you existing project, copy all files into the webroot folder.
This folder is mounted as volume to the container and it's defined in your ```docker-compose.yml```:

```yaml
    php7.0:
      ...
      volumes:
        - ./src/:/data/
      ...
```

You mount your local folder ```src/``` (relative Path from docker-compose.yml) in your Docker container under ```/data```.

Apache2 webroot is defined as ```/data/source```, because I could install all OXID versions from Github
with a little bit less hassle.

## Setup
Now you can setup your Project by visiting [http://localhost:8082/setup/](http://localhost:8082) and follow the
installation steps.

Remember the values you defined in your `docker-compose.yml`. You enter `oxid_db`, when promted for the Database host,
because this is the id and domain name you defined by "linking" the container with `oxid_db`

    php5.6:
    ...
      links:
        - oxid_db

The default values for MySQL Database, username and password are `oxid`. The Database credentials are defined by
environemnt variables at the `oxid_db` container

```yaml
environment:
  MYSQL_ROOT_PASSWORD: root
  MYSQL_DATABASE:      oxid
  MYSQL_USER:          oxid
  MYSQL_PASSWORD:      oxid
```

## Import DB
You want to import your own database dump? No problem, you can access the Database with your own Tools, the port `3306`
is external accessible:

    oxid_db:
    ...
      ports:
        - "3306:3306"

or you can run an manual import you database dump or get the demo dump from Github

for OXID v6:

    curl -OL https://raw.githubusercontent.com/Mesa/oxid/master/data/oxid_v6.0.0.0.sql

for OXID v4.10

    curl -OL https://raw.githubusercontent.com/Mesa/oxid/master/data/oxid_v4.10.5.sql

to load the sql from github and save it local.

#### Import with Mysql-Client ####
You can pass the data to you local database and execute the SQL from you host machine on the database container by
executing:

    docker-compose exec oxid_db mysql -u root -p < oxid_v4.10.5.sql

This also applies to all other sql you want to execute, just omit the file redirection and you are ready to go with
the mysql client.

#### Import on startup ####

The better approach is to mount a local folder (```db-dumps```) containing you mysql dump to you database and on the first startup
this dump gets imported. Only on initalisation of your container all sql files located at this folder (```db-dumps```) get executed.

This means you can drop the mysql dump in this folder. Everytime you destroy / remove the mysql container all data will
be removed and get lost.

The next time you do ```docker-compose up```, the database container gets newly created and all ```.sql``` files located
in ```db-dumps``` get executed and your database is prefill with your data.

```yaml
oxid_db:
  ...
  volumes:
    - ./db-dumps/:/docker-entrypoint-initdb.d/
```

You can start and stop the container without any impact to your data, only removing the container will remove all your data.

### Backup Database ###

The database credentials are saved in every container in the ```/etc/my.cnf``` file. This file is updated on every
restart. So when you change your credentials, you change them in the ```docker-compose.yml``` stop and start your
container and everything should be working normal.

Only the database name has to be specified, the default value is ```oxid```.

```bash
docker exec -i oxid_db mysqldump [database name] > oxid.sql
```

### Reset password ###

When you lost your Admin password you can reset it to you environment variables `OXID_ADMIN_PASSWORD` and `OXID_ADMIN_USERNAME`
with the new script.

    docker-compose exec php5.6 /etc/reset_admin.sh

or

    docker-compose exec php7.0 /etc/reset_admin.sh

The variable can be defined in your `docker-compose.yml`.

Or you can do it by hand with this sql:

```sql
UPDATE oxuser
    SET OXPASSWORD = MD5( CONCAT( '${OXID_ADMIN_PASSWORD}',  '${NEW_SALT}' ) ) ,
    OXPASSSALT = hex('${NEW_SALT}'),
    OXUSERNAME = '${OXID_ADMIN_USERNAME}'
where oxid = 'oxdefaultadmin';
```

You have to replace all environment variables with your new values.


### Composer ###

You want to install you dependencies, composer is preinstalled and ready to use. The root folder for all executed commands
is ```/data```.

```bash
docker-compose exec php5.6 composer
```

The easiest way to update your project dependencies is to execute the ```bash``` in your container,

```bash
docker-compose exec php5.6 bash
```

change to your module folder and run all required composer commands.

Composer is globally installed, just enter ```composer``` and you see all
options etc...

Keep in mind. that composer installs all your dependencies dependent on the PHP version.



