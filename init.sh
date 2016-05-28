#!/bin/bash
set -e

NEW_SALT=$(cat /dev/urandom | tr -dc 'a-z0-9' | fold -w 32 | head -n 1)

if [ -f ${APACHE_PID_FILE} ]; then
    rm -f ${APACHE_PID_FILE}
fi

# Waiting for the database image
sleep 20 &&  \
mysql -h ${MYSQL_HOST} -u ${MYSQL_USER}  -p${MYSQL_PASSWORD} -e "
    UPDATE oxuser
    SET OXPASSWORD = MD5( CONCAT( '${OXID_ADMIN_PASSWORD}',  '${NEW_SALT}' ) ) ,
        OXPASSSALT = hex('${NEW_SALT}'),
        OXUSERNAME = '${OXID_ADMIN_USERNAME}'
    where oxid = 'oxdefaultadmin';" ${MYSQL_DATABASE} &

/usr/sbin/apache2ctl -D FOREGROUND