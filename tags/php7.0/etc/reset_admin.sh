#!/usr/bin/env bash

echo "Resetting username and password to environment variables from docker-compose.yml"

NEW_SALT=$(cat /dev/urandom | tr -dc 'a-z0-9' | fold -w 32 | head -n 1)

mysql -e "
    UPDATE oxuser
    SET OXPASSWORD = MD5( CONCAT( '${OXID_ADMIN_PASSWORD}',  '${NEW_SALT}' ) ) ,
        OXPASSSALT = hex('${NEW_SALT}'),
        OXUSERNAME = '${OXID_ADMIN_USERNAME}'
    where oxid = 'oxdefaultadmin';" ${MYSQL_DATABASE}
