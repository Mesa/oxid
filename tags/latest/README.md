OXID CE and PHP7.0
===================

This container is running Apache, PHP, composer and XDEBUG.
Now you can test you modules on every OXID Version out there.

Development was never so easy and we make OXID development great again.


## Getting started ( Quick start ) ##
Copy the files from [demo](https://github.com/Mesa/oxid/tree/master/demo) sub folder and run

```bash
docker-compose up
```
That's it, now you can access the shop at [http://localhost](http://localhost).
Or you can try the [manual](https://github.com/Mesa/oxid/tree/master/doc/manual_install.md) installation.
This could be handy if you don't want to start from scratch.

Don't forget to change your admin user name and password in your docker-compose.yml, the default values are "oxid".


```
OXID_ADMIN_PASSWORD: oxid
OXID_ADMIN_USERNAME: oxid
```


### Versions: ###
* OXID eShop Community Edition. 4.10.5
* Apache/2.4.18 (Ubuntu)
* Ubuntu 16.04
* PHP 5.6


##### License #####
The MIT License (MIT)