#!/bin/bash
set -e

# Sometimes apache misses to remove the pid file and the container wont start
if [ -f "${APACHE_PID_FILE}" ]; then
    rm -f "${APACHE_PID_FILE}"
fi

if [ ! -d ${DOCKER_DOCUMENT_ROOT}]; then
    mkdir -p ${DOCKER_DOCUMENT_ROOT}
fi

exec "$@"