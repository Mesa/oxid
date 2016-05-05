#!/usr/bin/env bash

docker images mesa/oxid

echo "Version nr.:"
read version

docker-compose build
docker tag oxiddocker_oxid mesa/oxid:latest
docker tag mesa/oxid:latest mesa/oxid:$version