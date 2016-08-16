#!/usr/bin/env bash

case $1 in
"sql")
    docker exec -i oxid mysql oxid -e "${2}"
   ;;

"import")
    docker exec -i oxid_db mysql oxid < oxid.sql
   ;;

"dump")
    docker exec -i oxid_db mysqldump oxid -u oxid -poxid > oxid.sql
   ;;

"import")
    docker exec -i oxid_db mysql oxid -u oxid -poxid < oxid.sql
   ;;
*)

esac
