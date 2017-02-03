FROM php:7.1-fpm

MAINTAINER Jason Raimondi <jason@raimondi.us>

ENV TERM xterm

RUN apt-key adv --keyserver hkp://pgp.mit.edu:80 --recv-keys 573BFD6B3D8FBC641079A6ABABF5BD827BD9BF62 \
    && echo "deb http://nginx.org/packages/mainline/debian/ jessie nginx" >> /etc/apt/sources.list \
    && apt-get update \
    && apt-get install -y \
        ca-certificates nginx pkg-config curl cron supervisor git vim mysql-client sqlite3 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* \
    && rm -rf /var/www/html \
    && rm /etc/nginx/conf.d/default.conf

RUN ln -s /usr/lib/libc-client.a /usr/lib/x86_64-linux-gnu/libc-client.a

RUN docker-php-ext-install tokenizer \
        mbstring \
        mcrypt \
        opcache \
        exif \
        pdo \
        pdo_mysql \
        zip

#RUN mkdir -p /usr/include \
#    && docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-png-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
#    && docker-php-ext-install gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

COPY ./etc/supervisord.conf /etc/supervisord.conf

COPY ./var/crontab.txt /var/crontab.txt
RUN crontab /var/crontab.txt
RUN chmod 600 /etc/crontab

COPY ./etc/nginx/nginx.conf /etc/nginx/nginx.conf
ADD ./etc/nginx/conf.d/ /etc/nginx/conf.d

ADD ./etc/php/fpm/php-fpm.d/ /usr/local/etc/php-fpm.d
COPY ./etc/php/fpm/php.ini /usr/local/etc/php/php.ini

WORKDIR /var/www/html

EXPOSE 80 443 9000

COPY ./bin/run.sh /usr/local/bin/
RUN chmod a+x /usr/local/bin/run.sh

CMD /usr/local/bin/run.sh
