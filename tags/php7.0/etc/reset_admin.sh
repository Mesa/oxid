#!/usr/bin/env bash

echo "Reset admin password to value of environment variable [OXID_ADMIN_PASSWORD]."
echo "To change value update you docker-compose.yml"

NEW_SALT=$(cat /dev/urandom | tr -dc 'a-z0-9' | fold -w 32 | head -n 1)

mysql -h ${MYSQL_HOST} -u ${MYSQL_USER} -p${MYSQL_PASSWORD} -e "
    UPDATE oxuser
    SET OXPASSWORD = MD5( CONCAT( '${OXID_ADMIN_PASSWORD}',  '${NEW_SALT}' ) ) ,
        OXPASSSALT = hex('${NEW_SALT}'),
        OXUSERNAME = '${OXID_ADMIN_USERNAME}'
    where oxid = 'oxdefaultadmin';" ${MYSQL_DATABASE}
