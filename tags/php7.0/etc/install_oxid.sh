#!/usr/bin/env bash


function logTime {
    DATE=$(date "+%d.%m.%y - %H:%M:%S")
    echo "${DATE}  |  ${1} "
}

if [ -z ${DOCKER_DOCUMENT_ROOT} ]; then
    logTime "Document root is undefined set env [DOCKER_DOCUMENT_ROOT]"
    exit 1
else
    logTime "Installing OXID CE to folder [${DOCKER_DOCUMENT_ROOT}]"
fi

if [ ! -d "${DOCKER_DOCUMENT_ROOT}" ]; then
    mkdir -p "${DOCKER_DOCUMENT_ROOT}"
fi

OXID_DEFAULT_VERSION="v4.10.5"
TMP_DIR=/tmp/oxid

if [ -z $1 ]; then
    OXID_VERSION=$OXID_DEFAULT_VERSION
else
    OXID_VERSION=$1
fi

OXID_BASE_VERSION=${OXID_VERSION:1:1}

logTime "Installing OXID CE version [$OXID_VERSION]"

if [ -d ${TMP_DIR} ]; then
    rm -rf ${TMP_DIR}
fi

mkdir -p ${TMP_DIR} && \
    cd ${TMP_DIR} && \
    git clone https://github.com/OXID-eSales/oxideshop_ce.git --branch ${OXID_VERSION} ${TMP_DIR} || exit 1

if [  $OXID_BASE_VERSION != "6" ]; then
    #################################################################
    # Install flow theme
    #################################################################
    logTime "Installing flow theme"
    git clone https://github.com/OXID-eSales/flow_theme.git ${TMP_DIR}/source/application/views/flow --branch b-1.0  || exit 1
    rsync -a source/application/views/flow/out/flow source/out/
fi

cd "${TMP_DIR}"

find ./ -type d -name .git -exec rm -rf "${TMP_DIR}/{}" \; &>/dev/null


if [ -d "${TMP_DIR}/source/setup" ]; then

    #################################################################
    # Update project setup encoding
    #################################################################
    logTime "Changing setup directory to utf8 encoding"

    cd ${TMP_DIR}/source/setup/
    find ./ -type f -name "*.php" | xargs -i bash -c "iconv -f latin1 -t UTF-8 {} > ${DOCKER_DOCUMENT_ROOT}setup/{}"
fi

# Dont override an existing configration file
if [ -f "${DOCKER_DOCUMENT_ROOT}config.inc.php" ]; then

    rsync -a "${TMP_DIR}/" "/data/" --delete \
        --exclude=.git/ \
        --exclude=out/ \
        --exclude=modules \
        --exclude=vendor \
        --exclude=log \
        --exclude=export \
        --exclude=config.inc.php

    chmod -R 0440 "${DOCKER_DOCUMENT_ROOT}config.inc.php"
    chmod -R 0440 "${DOCKER_DOCUMENT_ROOT}.htaccess"

else

    rsync -a "${TMP_DIR}/" "/data/" --delete \
        --exclude=.git/ \
        --exclude=out/ \
        --exclude=vendor \
        --exclude=log \
        --exclude=export \
        --exclude=modules

    # Not all version on github contain the config.inc.php.dist
    if [ -f ${DOCKER_DOCUMENT_ROOT}config.inc.php.dist ]; then
        cp "${DOCKER_DOCUMENT_ROOT}config.inc.php.dist" "${DOCKER_DOCUMENT_ROOT}config.inc.php"
    fi

    #################################################################
    # Update project config
    #################################################################
    source update_config.sh

    chmod -R 0777 "${DOCKER_DOCUMENT_ROOT}config.inc.php"
    chmod -R 0777 "${DOCKER_DOCUMENT_ROOT}.htaccess"
fi

if [ ! -d "${DOCKER_DOCUMENT_ROOT}log" ]; then
    mkdir "${DOCKER_DOCUMENT_ROOT}log"
fi

if [ ! -d "${DOCKER_DOCUMENT_ROOT}export" ]; then
    mkdir "${DOCKER_DOCUMENT_ROOT}export"
fi

rsync -a "${TMP_DIR}/source/out" "${DOCKER_DOCUMENT_ROOT}"
rsync -a "${TMP_DIR}/source/modules" "${DOCKER_DOCUMENT_ROOT}"

cd /data/

if [ -f composer.json ]; then
    composer install --no-dev

    if [ ${OXID_BASE_VERSION} = "6" ]; then
        composer require oxid-esales/oxideshop-demodata-ce:dev-b-6.0
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
