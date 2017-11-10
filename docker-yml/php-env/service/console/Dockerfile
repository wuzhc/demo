FROM phusion/baseimage:latest 
RUN apt-get update && apt-get install -y git curl wget cron vim locales libfreetype6-dev php7.1-cli


RUN git config --global user.name "wuzhc" \
	&& git config --global user.email "wuzhc2016@163.com"

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.bashrc \ # 用户目录下的私有环境变量
    && . ~/.bashrc \
    && composer config -g repo.packagist composer https://packagist.phpcomposer.com # 切换到国内源

