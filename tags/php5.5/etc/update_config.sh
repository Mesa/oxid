#!/usr/bin/env bash


declare -A config

config["iDebug"]="OXID_IDEBUG"
config["sShopDir"]="DOCKER_DOCUMENT_ROOT"
config["dbHost"]="MYSQL_HOST"
config["dbName"]="MYSQL_DATABASE"
config["dbUser"]="MYSQL_USER"
config["dbPwd"]="MYSQL_PASSWORD"
config["sAdminEmail"]="DOCKER_SERVER_ADMIN"
config["sShopURL"]="OXID_SHOP_URL"
config["sSSLShopURL"]="OXID_SHOP_SSL_URL"
config["iUtfMode"]="OXID_UTF_MODE"
config["sCompileDir"]="OXID_COMPILE_DIR"



echo "#########################"
echo "#    Updating config    #"
echo "#########################"

for i in "${!config[@]}"
do
    sed -i "s/this->${i}.\+/this->${i} = getenv('${config[$i]}');/" ${DOCKER_DOCUMENT_ROOT}config.inc.php
    grep "this->${i}" ${DOCKER_DOCUMENT_ROOT}config.inc.php
done

echo "#########################"

NEW_UUID=$(cat /dev/urandom | tr -dc 'a-zA-Z0-9' | fold -w 32 | head -n 1)
sed -i "s/<captchaKey>/${NEW_UUID}/" ${DOCKER_DOCUMENT_ROOT}config.inc.php
sed -i "s/date_default_timezone_set('Europe\/Berlin');/date_default_timezone_set(getenv('PHP_DATE_TIMEZONE'));/" ${DOCKER_DOCUMENT_ROOT}config.inc.php