FROM ubuntu:16.04

MAINTAINER Daniel Langemann <daniel.langemann@gmx.de>

RUN echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu xenial main" > /etc/apt/sources.list.d/ondrej_php.list; \
    echo "deb-src http://ppa.launchpad.net/ondrej/php/ubuntu xenial main " >> /etc/apt/sources.list.d/ondrej_php.list; \
    apt-key adv --recv-keys --keyserver keyserver.ubuntu.com E5267A6C

RUN apt-get update; apt-get -y upgrade --fix-missing;
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install \
    apache2 \
    libapache2-mod-php5.6 \
    php5.6-mysql \
    php5.6-gd \
    php5.6-xml \
    php5.6-json \
    php5.6-curl \
    curl \
    mysql-client \
    && apt-get autoremove -y \
    && apt-get autoclean -y \
    && a2enmod php5.6 \
    && a2enmod rewrite

ENV OXID_VERSION 4.10.0

# PHP configuration
ENV PHP_ERROR_REPORTING "E_ERROR | E_WARNING | E_PARSE"
ENV PHP_MEMORY_LIMIT "256M"
ENV PHP_DATE_TIMEZONE "Europe/Berlin"
ENV PHP_DISPLAY_ERRORS "Off"
ENV PHP_UPLOAD_MAX_FILESIZE  "8m"

# Apache configuration
ENV DOCKER_DOCUMENT_ROOT "/data/"
ENV DOCKER_SERVER_ADMIN "admin@localhost"
ENV DOCKER_ERROR_LOG "/dev/stdout"
ENV DOCKER_CUSTOM_LOG "/dev/stdout combined"
ENV DOCKER_ALLOW_OVERRIDE "All"

ENV APACHE_RUN_USER "www-data"
ENV APACHE_RUN_GROUP "www-data"
ENV APACHE_LOG_DIR "/dev/stdout"
ENV APACHE_LOCK_DIR "/var/lock/apache"
ENV APACHE_PID_FILE "/tmp/apache2.pid"
ENV APACHE_SERVERNAME "localhost"

# admin credentials are changed on every container run
ENV OXID_ADMIN_PASSWORD "docker"
ENV OXID_ADMIN_USERNAME "docker"

# OXID configuration for config.inc.php
ENV OXID_SHOP_URL "http://localhost"
ENV OXID_UTF_MODE 1
ENV OXID_IDEBUG 0
ENV OXID_COMPILE_DIR "/tmp"

# OXID configuration, equal names for mariaDB/mysql container vars
ENV MYSQL_HOST "oxid_db"
ENV MYSQL_USER "oxid"
ENV MYSQL_PASSWORD "oxid"
ENV MYSQL_DATABASE "oxid"

COPY apache-config.conf /etc/apache2/sites-enabled/000-default.conf
COPY php.ini /etc/php5/apache2/php.ini
RUN mkdir /data; \
    cd /tmp; \
    wget https://github.com/OXID-eSales/oxideshop_ce/archive/$OXID_VERSION.zip; unzip $OXID_VERSION.zip "/oxideshop_ce-${OXID_VERSION}/source/*" -d /tmp/oxid ; \
    mv /tmp/oxid/oxideshop_ce-${OXID_VERSION}/source/* /data; \
    rm -rf /tmp/oxid;

RUN chown -R www-data:www-data /data; \
    chmod -R ug+rwx /data; \
    chmod -R 0770 /data/out/media ; \
    chmod -R 0770 /data/out/pictures; \
    chmod -R 0770 /data/log; \
    chmod -R 0770 /data/tmp; \
    chmod -R 0770 /data/export; \
    chmod -R 0440 /data/config.inc.php; \
    chmod 0770 /data/.htaccess; \
    rm /tmp/oxid.tar.gz


# Set apache server name to global variable
RUN echo "ServerName ${APACHE_SERVERNAME}" | tee /etc/apache2/conf-available/fqdn.conf ; \
    ln -s /etc/apache2/conf-available/fqdn.conf /etc/apache2/conf-enabled/; \
    chmod 0444 /data/config.inc.php; \
    sed -i "s/Directory \/var\/www\//Directory \$\{DOCKER_DOCUMENT_ROOT\}/" /etc/apache2/apache2.conf; \
    sed -i "s/'<dbHost_ce>'/getenv('MYSQL_HOST')/" /data/config.inc.php; \
    sed -i "s/'<dbName_ce>'/getenv('MYSQL_DATABASE')/" /data/config.inc.php; \
    sed -i "s/'<dbUser_ce>'/getenv('MYSQL_USER')/" /data/config.inc.php; \
    sed -i "s/'<dbPwd_ce>'/getenv('MYSQL_PASSWORD')/" /data/config.inc.php; \
    sed -i "s/'<sShopURL_ce>'/getenv('OXID_SHOP_URL')/" /data/config.inc.php; \
    sed -i "s/'<sShopDir_ce>'/getenv('DOCKER_DOCUMENT_ROOT')/" /data/config.inc.php; \
    sed -i "s/'<iUtfMode>'/getenv('OXID_UTF_MODE')/" /data/config.inc.php; \
    sed -i "s/'<sCompileDir_ce>'/getenv('OXID_COMPILE_DIR')/" /data/config.inc.php; \
    sed -i "s/'<iUtfMode>'/getenv('OXID_IDEBUG')/" /data/config.inc.php;

VOLUME /data/modules
VOLUME /data/application/views
VOLUME /data/out

EXPOSE 80

COPY init.sh /tmp/
RUN chmod 0777 /tmp/init.sh

EXPOSE 80
CMD ["/tmp/init.sh"]