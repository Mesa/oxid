Oxid Docker
===========

This is an unofficial Docker image for OXID eShop Community Edition.
This images are based on Ubuntu, Apache2, PHP and support installing the OXID eshop CE version of you choice.

### Update - new tags
With the last update I added two container for development purposes. The original container is
continued with the tag "latest" and all other different setups will get their own tag like "php5.6", "php7.0" and
so on.

These containers are build for development purposes and not for production. I want to provide an full testing environment
for different OXID and PHP versions.

## Tags
* latest [README](https://github.com/Mesa/oxid/tree/master/tags/latest/README.md)
* php5.6 [README](https://github.com/Mesa/oxid/tree/master/tags/php5.6/README.md) - composer - IONCUBE - Zend Guard Loader - XDEBUG
* php7.0 [README](https://github.com/Mesa/oxid/tree/master/tags/php7.0/README.md) - composer - XDEBUG

## Changelog

[CHANGELOG.md](https://github.com/Mesa/oxid/tree/master/CHANGELOG.md)


#### Requirements ####
You need:
- docker
- [docker-compose](https://docs.docker.com/compose/) (required to use docker-compose.yml files)

## Getting started ( Quick start ) ##

#### Single container
If you want to run only one oxid container for testing the latest OXID version (4.10.5) you can go with
the ```1.1.6``` or ```latest``` tag. All information to get oxid up in running in no time is
located [here](https://github.com/Mesa/oxid/tree/master/tags/latest/README.md)

#### Development setup
When you want to test your module on multiple PHP Versions or not the latest oxid version, then you find all information
under [doc/development_setup.md](https://github.com/Mesa/oxid/tree/master/doc/development_setup.md).


#### More Infromation
- Environment Variables [doc/variables](https://github.com/Mesa/oxid/tree/master/doc/variables.md)
- Manual Installation [doc/manual_install](https://github.com/Mesa/oxid/tree/master/doc/manual_install.md)

##### License #####
The MIT License (MIT)