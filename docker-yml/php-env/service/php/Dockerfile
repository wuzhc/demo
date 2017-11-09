FROM php:7.0-fpm
RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mysqli pdo_mysql zip 

# 是否安装redis
ARG IS_INSTALL_REDIS=false
RUN if [ ${IS_INSTALL_REDIS} = true ]; then \
    pecl install redis \
    && docker-php-ext-enable redis \
;fi

# 是否安装memcached
ARG IS_INSTALL_MEMCACHED=false
RUN if [ ${IS_INSTALL_MEMCACHED} = true ]; then \
	pecl install memcached \
	&& docker-php-ext-enabled memcached \
;fi