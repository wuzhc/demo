FROM php:7.1-fpm
RUN apt-get update && apt-get install -y git curl wget cron vim locales libfreetype6-dev 

RUN git config --global user.name "wuzhc" \
	&& git config --global user.email "wuzhc2016@163.com"

# 国内安装
# RUN curl -sS https://getcomposer.org/installer | php \
#    && mv composer.phar /usr/local/bin/composer \
#    && echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.bashrc \
#    && . ~/.bashrc \
#    && composer config -g repo.packagist composer https://packagist.phpcomposer.com # 切换到国内源

RUN curl -sS https://getcomposer.org/installer | php \
   && mv composer.phar /usr/local/bin/composer \
   && echo 'export PATH="$PATH:$HOME/.composer/vendor/bin"' >> ~/.bashrc \
   && . ~/.bashrc 
