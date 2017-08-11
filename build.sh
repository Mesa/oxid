#!/usr/bin/env bash

PROJECT_NAME="mesa/oxid"

# Build specified image only
if [ ! -z $1 ]; then

    SRC_PATH=tags/${1}

    if [ ! -d "${SRC_PATH}" ]; then
        echo "Path not found"
        exit
    fi

    if [ ! -f "${SRC_PATH}/Dockerfile" ]; then
        echo "Dockerfile not found"
        exit
    fi

     docker build --tag=${PROJECT_NAME}:${1} ${SRC_PATH}

    exit 0
fi

# Build all images
for dir in ./tags/*/
do
    dir=${dir%*/}
    name=${dir##*/}

    if [ -f "${dir}/Dockerfile" ] ; then
        docker build --tag=${PROJECT_NAME}:${name} ${dir}
    fi
done
