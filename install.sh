#!/usr/bin/env bash

cd /tmp;
wget https://github.com/OXID-eSales/oxideshop_ce/archive/v$OXID_VERSION.zip &&
unzip v${OXID_VERSION}.zip "oxideshop_ce-${OXID_VERSION}/source/*" -d /tmp/oxid &&
mv /tmp/oxid/oxideshop_ce-${OXID_VERSION}/source /data;
rm -rf /tmp/oxid

rm "/tmp/v${OXID_VERSION}.zip"

# remove setup directory
rm -rf /data/setup
