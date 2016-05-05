#!/usr/bin/env bash

case $1 in
"dump")
    docker exec -i oxid_db mysqldump oxid -u oxid -poxid > oxid.sql
   ;;

"import")
    docker exec -i oxid_db mysql oxid -u oxid -poxid < oxid.sql
   ;;

*)
    echo 'Use "dump" or "import" as argument'

esac
