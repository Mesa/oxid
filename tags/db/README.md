OXID eShop - Docker
===========

The complete documentation can be found on [github](https://mesa.github.io/oxid/).

This are unofficial Docker images for OXID eShop Community Edition (for Development purpose).
This images are based on Ubuntu, Apache2, PHP and with script based install helper for the OXID eshop CE version of your
choice.

### Update - new tags
With the last update I added two container for development purposes. The original container is continued with the tag
"latest" and all other different setups will get their own tag like "php5.6", "php7.0" and so on.

These containers are build for development purposes and not for production. I want to provide an full testing environment
for different OXID and PHP versions.

## Tags
* latest
* php5.6 - composer - IONCUBE - Zend Guard Loader - XDEBUG
* php7.0 - composer - XDEBUG
* db - mysql 5.5 - with collation and connection utf8 enabled

## Changelog

[CHANGELOG](http://mesa.github.io/oxid/CHANGELOG)


#### Requirements ####
You need:
- docker
- [docker-compose](https://docs.docker.com/compose/) (required to use docker-compose.yml files)

## Getting started ( Quick start ) ##

#### Single container
If you want to run only one oxid container for testing the latest OXID version (4.10.5) you can go with
the ```latest``` tag. All information to get oxid up in running in no time is located
[here](http://mesa.github.io/oxid/doc/old_setup)

#### Development setup
When you want to test your module on multiple PHP Versions or not the latest oxid version, then you find all information
under [doc/development_setup](http://mesa.github.io/oxid/doc/development_setup).


#### More Infromation
- Environment Variables [doc/variables](http://mesa.github.io/oxid/doc/variables)
- Manual Installation [doc/manual_install](http://mesa.github.io/oxid/doc/manual_install)

##### License #####
The MIT License (MIT)