FROM ubuntu:16.04

MAINTAINER Daniel Langemann <daniel.langemann@gmx.de>

RUN echo "deb http://ppa.launchpad.net/ondrej/php/ubuntu xenial main" > /etc/apt/sources.list.d/ondrej_php.list; \
    echo "deb-src http://ppa.launchpad.net/ondrej/php/ubuntu xenial main " >> /etc/apt/sources.list.d/ondrej_php.list; \
    apt-key adv --recv-keys --keyserver keyserver.ubuntu.com E5267A6C

RUN DEBIAN_FRONTEND=noninteractive ; apt-get update; apt-get -y upgrade --fix-missing ; apt-get -y install \
    apache2 \
    libapache2-mod-php5.6 \
    php5.6-mysql \
    php5.6-gd \
    php5.6-xml \
    php5.6-json \
    php5.6-curl \
    php5.6-mbstring \
    php5.6-bcmath \
    php5.6-soap \
    curl \
    unzip \
    git \
    mysql-client \
    && apt-get autoremove -y \
    && apt-get autoclean -y \
    && a2enmod php5.6 \
    && a2enmod rewrite

# Url to OXID repository on github
ENV OXID_SRC_URL "https://github.com/OXID-eSales/oxideshop_ce/archive/master.zip"

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

# admin credentials are updated on every container start
ENV OXID_ADMIN_PASSWORD "docker"
ENV OXID_ADMIN_USERNAME "docker"

# OXID configuration for config.inc.php
ENV OXID_SHOP_URL "http://localhost"
ENV OXID_UTF_MODE 1
ENV OXID_IDEBUG 0
ENV OXID_COMPILE_DIR "/data/tmp"

# OXID configuration, equal names for mariaDB/mysql container vars
ENV MYSQL_HOST "oxid_db"
ENV MYSQL_USER "oxid"
ENV MYSQL_PASSWORD "oxid"
ENV MYSQL_DATABASE "oxid"

COPY etc/000-default.conf /etc/apache2/site-available/000-default.conf
COPY etc/php.ini /etc/php5/apache2/php.ini
COPY etc/docker-entrypoint.sh /etc/docker-entrypoint.sh

RUN mkdir /oxid_tmp ; \
    cd /oxid_tmp ; \
    curl -OL ${OXID_SRC_URL} ; \
    unzip master.zip ; \
    rm master.zip ; \
    mkdir -p ${DOCKER_DOCUMENT_ROOT} ; \
    cp -av oxideshop_ce-master/source/* ${DOCKER_DOCUMENT_ROOT} ; \
    cp oxideshop_ce-master/source/.htaccess ${DOCKER_DOCUMENT_ROOT} ; \
    cd / ; \
    rm -rf /oxid_tmp ; \
    cp ${DOCKER_DOCUMENT_ROOT}config.inc.php.dist ${DOCKER_DOCUMENT_ROOT}config.inc.php ; \
    chown -R www-data:www-data ${DOCKER_DOCUMENT_ROOT} ; \
    chmod -R ug+rwx ${DOCKER_DOCUMENT_ROOT} ; \
    chmod -R 0770 ${DOCKER_DOCUMENT_ROOT}out/media ; \
    chmod -R 0770 ${DOCKER_DOCUMENT_ROOT}out/pictures; \
    chmod -R 0770 ${DOCKER_DOCUMENT_ROOT}log ; \
    chmod -R 0770 ${DOCKER_DOCUMENT_ROOT}tmp ; \
    chmod -R 0770 ${DOCKER_DOCUMENT_ROOT}export ; \
    chmod -R 0440 ${DOCKER_DOCUMENT_ROOT}config.inc.php ; \
    chmod 0770 ${DOCKER_DOCUMENT_ROOT}.htaccess ; \
    chmod +x /etc/docker-entrypoint.sh

RUN echo "ServerName ${APACHE_SERVERNAME}" | tee /etc/apache2/conf-available/fqdn.conf ; \
    ln -s /etc/apache2/conf-available/fqdn.conf /etc/apache2/conf-enabled/ ; \
    chmod 0444 ${DOCKER_DOCUMENT_ROOT}config.inc.php; \
    sed -i "s/Directory \/var\/www\//Directory \$\{DOCKER_DOCUMENT_ROOT\}/" /etc/apache2/apache2.conf; \
    sed -i "s/'<dbHost_ce>'/getenv('MYSQL_HOST')/" ${DOCKER_DOCUMENT_ROOT}config.inc.php; \
    sed -i "s/'<dbName_ce>'/getenv('MYSQL_DATABASE')/" ${DOCKER_DOCUMENT_ROOT}config.inc.php; \
    sed -i "s/'<dbUser_ce>'/getenv('MYSQL_USER')/" ${DOCKER_DOCUMENT_ROOT}config.inc.php; \
    sed -i "s/'<dbPwd_ce>'/getenv('MYSQL_PASSWORD')/" ${DOCKER_DOCUMENT_ROOT}config.inc.php; \
    sed -i "s/'<sShopURL_ce>'/getenv('OXID_SHOP_URL')/" ${DOCKER_DOCUMENT_ROOT}config.inc.php; \
    sed -i "s/'<sShopDir_ce>'/getenv('DOCKER_DOCUMENT_ROOT')/" ${DOCKER_DOCUMENT_ROOT}config.inc.php; \
    sed -i "s/'<iUtfMode>'/getenv('OXID_UTF_MODE')/" ${DOCKER_DOCUMENT_ROOT}config.inc.php; \
    sed -i "s/'<sCompileDir_ce>'/getenv('OXID_COMPILE_DIR')/" ${DOCKER_DOCUMENT_ROOT}config.inc.php; \
    sed -i "s/'<iUtfMode>'/getenv('OXID_IDEBUG')/" ${DOCKER_DOCUMENT_ROOT}config.inc.php;

VOLUME ${DOCKER_DOCUMENT_ROOT}modules
VOLUME ${DOCKER_DOCUMENT_ROOT}application/views
VOLUME ${DOCKER_DOCUMENT_ROOT}out

EXPOSE 80

WORKDIR ${DOCKER_DOCUMENT_ROOT}

ENTRYPOINT ["/etc/docker-entrypoint.sh"]

CMD /usr/sbin/apache2ctl -D FOREGROUND