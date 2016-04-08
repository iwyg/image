#!/bin/sh

set -e

sudo apt-get install -y liblcms2-2 liblcms2-utils libmagickcore-dev libmagickwand-dev imagemagick

git clone https://github.com/mkoppanen/imagick.git

cd imagick
phpize
./configure --with-php-config=`which php-config`
sudo make
sudo make install

PHP_VERSION=`phpenv version-name`;
echo "extension = imagick.so" >> ~/.phpenv/versions/$PHP_VERSION/etc/php.ini
php --ri imagick;
cd ..
