## Manual installation ##

You can install oxid on your own. Just create an empty Database Schema and adjust the environment variables
named ```MYSQL_HOST``` ```MYSQL_USER``` ```MYSQL_DATABASE``` and ```MYSQL_PASSWORD``` to your own configuration.

```yaml
version: '2'
services:
    oxid:
        image: mesa/oxid:1.1.6
        container_name: oxid
        ports:
            - "80:80"
        links:
            - oxid_db:oxid_db
        volumes:
            - "/etc/localtime:/etc/localtime"
#           - "./data:/data" # Mount your existing project direct
            # Or you can only mount your modules
#           - "./public/out/my_module:/data/out/my_module"
#           - "./public/modules/my_module:/data/modules/my_module"

        environment:
            MYSQL_HOST: oxid_db
            MYSQL_DATABASE: oxid
            MYSQL_USER: oxid
            MYSQL_PASSWORD: oxid
            OXID_SHOP_URL: "http://localhost"
            OXID_IDEBUG: 0
            OXID_ADMIN_PASSWORD: oxid
            OXID_ADMIN_USERNAME: oxid
            OXID_COMPILE_DIR: "/data/tmp"

    oxid_db:
        image: mariadb:latest
        container_name: oxid_db
#       Open ports for external access in development only
#       ports:
#           - "3306:3306"
        volumes:
            - "./db-dumps/:/docker-entrypoint-initdb.d/"
#           Store your mysql db local in this mounted folder
#           - "./mysql:/var/lib/mysql"

        environment:
            MYSQL_ROOT_PASSWORD:  25d8341295ed88e4bcfc871970a5bda4
            MYSQL_DATABASE:       oxid
            MYSQL_USER:           oxid
            MYSQL_PASSWORD:       oxid
```

Create a folder named ```db-dumps``` and put the [demo/db-dumps/oxid.sql](demo/db-dumps/oxid.sql) in there.
During the first image run, the sql dump will be imported, with an existing database (after the first container run)
the sql dump file will be ignored.

Execute:

```bash
docker-compose up
```
and visit [http://localhost](http://localhost).