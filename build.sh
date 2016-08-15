#!/usr/bin/env bash

#  Get script path to ignore the current working directory and execute this script from every where
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
pushd $DIR

# Build script for testing the Docker container
docker-compose build
docker tag oxiddocker_oxid mesa/oxid:latest

docker rmi oxiddocker_oxid

popd