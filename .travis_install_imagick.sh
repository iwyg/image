#!/bin/sh

set -e

# errors in install: invalid option -- 'y'
sudo install libmagickcore-dev libmagickwand-dev imagemagick

git clone https://github.com/mkoppanen/imagick.git

cd imagick
phpize
./configure --with-php-config=`which php-config`
sudo make
sudo make install

PHP_VERSION = `phpenv version-name`;
echo "extension = imagick.so" >> ~/.phpenv/versions/$PHP_VERSION/etc/php.ini
php --ri imagick;
cd ..
