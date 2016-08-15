#!/bin/bash
set -e

NEW_SALT=$(cat /dev/urandom | tr -dc 'a-z0-9' | fold -w 32 | head -n 1)

if [ -f ${APACHE_PID_FILE} ]; then
    rm -f ${APACHE_PID_FILE}
fi


i=0

echo "[client]" > ~/.my.cnf
echo "user=${MYSQL_USER}" >> ~/.my.cnf
echo "password=${MYSQL_PASSWORD}" >> ~/.my.cnf

chmod 0600 ~/.my.cnf

for i in {0..60}; do
    if echo 'SELECT 1' | mysql &> /dev/null; then
        echo "MySQL conenction established"

        mysql -h ${MYSQL_HOST} -e "
            UPDATE oxuser
            SET OXPASSWORD = MD5( CONCAT( '${OXID_ADMIN_PASSWORD}',  '${NEW_SALT}' ) ) ,
                OXPASSSALT = hex('${NEW_SALT}'),
                OXUSERNAME = '${OXID_ADMIN_USERNAME}'
            where oxid = 'oxdefaultadmin';" ${MYSQL_DATABASE}

        break
    fi

    echo "${i} - Trying to connect to MySQL ...  "
    sleep 1
done


if [ ${i} -eq 30 ]; then
    echo "No connection to MySQL could be established"
fi

exec "$@"