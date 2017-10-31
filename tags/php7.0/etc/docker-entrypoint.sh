#!/bin/bash
set -e

# Sometimes apache misses to remove the pid file and the container won't start
if [ -f "${APACHE_PID_FILE}" ]; then
    rm -f "${APACHE_PID_FILE}"
fi

if [ ! -d "${DOCKER_DOCUMENT_ROOT}" ]; then
    mkdir -p "${DOCKER_DOCUMENT_ROOT}"
fi

if [ ! -d "${OXID_COMPILE_DIR}" ]; then
    mkdir -p "${OXID_COMPILE_DIR}"
fi

chmod -R 0777 ${OXID_COMPILE_DIR}

if [ -d "${DOCKER_DOCUMENT_ROOT}setup" ]; then
    rm -rf "${DOCKER_DOCUMENT_ROOT}setup"
fi

echo "[client]" > /etc/my.cnf
echo "user=\"${MYSQL_USER}\"" >> /etc/my.cnf
echo "password=\"${MYSQL_PASSWORD}\"" >> /etc/my.cnf
echo "host=\"${MYSQL_HOST}\"" >> /etc/my.cnf
echo "default-character-set=utf8" >> /etc/my.cnf

exec "$@"