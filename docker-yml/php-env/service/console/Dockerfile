FROM phusion/baseimage:latest 
RUN apt-get update && apt-get install -y git curl wget cron vim locales libfreetype6-dev

RUN git config --global user.name "wuzhc" \
	&& git config --global user.email "wuzhc2016@163.com"

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod u+x /usr/local/bin/composer

