#!/usr/bin/env bash

function logTime {
    DATE=$(date "+%d.%m.%y - %H:%M:%S")
    echo "${DATE}  |  ${1} "
}

FORCE_FRESH="false"
OXID_DEFAULT_VERSION="v4.10.5"
TMP_DIR=/tmp/oxid

while [[ $# -gt 0 ]] ; do

    key="$1"

    case $key in
        -f)
            FORCE_FRESH="true"
            logTime "Forced install."
        ;;
        -v)
            OXID_VERSION="${2}"
            shift
        ;;
        -db|--db)
            logTime "Dropping database anr recreating with character set utf8"
            echo "DROP DATABASE IF EXISTS ${MYSQL_DATABASE}" | mysql -u ${MYSQL_USER} -p${MYSQL_PASSWORD} &>/dev/null
            echo "CREATE DATABASE ${MYSQL_DATABASE} DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;"  | mysql -u ${MYSQL_USER} -p${MYSQL_PASSWORD} &>/dev/null
        ;;

    esac

    shift
done

if [ $FORCE_FRESH = "true" ] ; then
    logTime "Removing config file [${DOCKER_DOCUMENT_ROOT}config.inc.php]"
    rm "${DOCKER_DOCUMENT_ROOT}config.inc.php"
fi

if [ -d "${TMP_DIR}" ]; then
    logTime "Removed old oxid installation from tmp dir"
    rm -rf "${TMP_DIR}"
fi

if [ -z ${OXID_VERSION} ]; then
    logTime "Setting OXID CE version to default value ${OXID_DEFAULT_VERSION}"
    OXID_VERSION=${OXID_DEFAULT_VERSION}
fi

if [ -z ${DOCKER_DOCUMENT_ROOT} ]; then
    logTime "Document root is undefined set env [DOCKER_DOCUMENT_ROOT]"
    exit 1
else
    logTime "Installing OXID CE to folder [${DOCKER_DOCUMENT_ROOT}]"
fi

if [ ! -d "${DOCKER_DOCUMENT_ROOT}" ]; then
    logTime "Created document root folder ${DOCKER_DOCUMENT_ROOT}"
    mkdir -p "${DOCKER_DOCUMENT_ROOT}"
fi

OXID_BASE_VERSION=${OXID_VERSION:1:1}

logTime "Installing OXID CE version [${OXID_VERSION}]"

mkdir -p ${TMP_DIR} && \
    cd ${TMP_DIR} && \
    git clone https://github.com/OXID-eSales/oxideshop_ce.git --branch ${OXID_VERSION} ${TMP_DIR} || exit 1

if [ $OXID_BASE_VERSION != "6" ]; then
    #################################################################
    # Install flow theme
    #################################################################
    logTime "Installing flow theme"
    git clone https://github.com/OXID-eSales/flow_theme.git ${TMP_DIR}/source/application/views/flow --branch b-1.0  || exit 1
    rsync -a source/application/views/flow/out/flow source/out/
    cd source/setup/sql/
    curl -O "https://raw.githubusercontent.com/OXID-eSales/oxideshop_demodata_ce/b-5.3/src/demodata.sql"
fi

cd "${TMP_DIR}"

find ./ -type d -name .git -exec rm -rf "${TMP_DIR}/{}" \; &>/dev/null

logTime "Syncing cloned oxid to webroot"

rsync -a "${TMP_DIR}/" "/data/" \
        --exclude=.git/ \
        --exclude=export \
        --exclude=config.inc.php

if [ ! -f "${DOCKER_DOCUMENT_ROOT}config.inc.php" ]; then

    # Not all version on github contain the config.inc.php.dist
    if [ -f ${DOCKER_DOCUMENT_ROOT}config.inc.php.dist ]; then
        cp "${DOCKER_DOCUMENT_ROOT}config.inc.php.dist" "${DOCKER_DOCUMENT_ROOT}config.inc.php"
    else
        cp "${TMP_DIR}/source/config.inc.php" "${DOCKER_DOCUMENT_ROOT}config.inc.php"
    fi

    #################################################################
    # Update project config
    #################################################################
    source /etc/update_config.sh
fi


# Change file encoding after sync to webroot
#if [ -d "${TMP_DIR}/source/setup" ]; then
#
#    #################################################################
#    # Update project setup encoding
#    #################################################################
#    logTime "Changing setup directory to utf8 encoding"
#
#    cd ${TMP_DIR}/source/setup/
#    find ./ -type f -name "*.php" | xargs -i bash -c "iconv -f latin1 -t UTF-8 {} > ${DOCKER_DOCUMENT_ROOT}setup/{}"
#fi

if [ ! -d "${DOCKER_DOCUMENT_ROOT}log" ]; then
    mkdir -p "${DOCKER_DOCUMENT_ROOT}log"
fi

if [ ! -d "${DOCKER_DOCUMENT_ROOT}export" ]; then
    mkdir -p "${DOCKER_DOCUMENT_ROOT}export"
fi

cd /data/

if [ -f composer.json ]; then
    if [ -d vendor ]; then
        rm -rf vendor
    fi

    composer install --no-dev --no-interaction

    if [ ${OXID_BASE_VERSION} = "6" ]; then
        composer require --no-update oxid-esales/oxideshop-demodata-ce:dev-b-6.0 && \
        composer update --no-dev --no-interaction
        rsync -a "${TMP_DIR}/source/Setup" "${DOCKER_DOCUMENT_ROOT}"
    fi
fi

#################################################################
# Change file modes and ownership
#################################################################
chown -R www-data:www-data "${DOCKER_DOCUMENT_ROOT}"
chmod -R ug+rwx "${DOCKER_DOCUMENT_ROOT}"
chmod -R 0770 "${DOCKER_DOCUMENT_ROOT}out/media"
chmod -R 0770 "${DOCKER_DOCUMENT_ROOT}out/pictures"
chmod -R 0770 "${DOCKER_DOCUMENT_ROOT}log"
chmod -R 0770 "${DOCKER_DOCUMENT_ROOT}tmp"
chmod -R 0770 "${DOCKER_DOCUMENT_ROOT}export"
chmod -R 0777 "${DOCKER_DOCUMENT_ROOT}config.inc.php"
chmod -R 0777 "${DOCKER_DOCUMENT_ROOT}.htaccess"


# clear Cache
rm -rf "${DOCKER_DOCUMENT_ROOT}tmp/*"
rm -rf "${TMP_DIR}"
