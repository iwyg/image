set -x
sudo apt-get remove -y imagemagick libmagickcore-dev libmagickwand-dev
sudo apt-get install -y libtiff-dev libjpeg-dev libdjvulibre-dev libwmf-dev pkg-config
echo '' > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini

#if [ "$TRAVIS_PHP_VERSION" = 'hhvm' ] || [ "$TRAVIS_PHP_VERSION" = 'hhvm-nightly' ] ; then
#    echo "skipping imagick installation"
if ["$IMAGE_DRIVER" = 'imagick'] ; then
	pear config-set preferred_state beta
	printf "\n" | pecl install imagick
	echo "extension = imagick.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	php --ri magick 
elif ["$IMAGE_DRIVER" = 'gmagick'] ; then
	#sudo apt-get install -y graphicsmagick libgraphicsmagick1-dev;
    #wget http://pecl.php.net/get/gmagick-1.1.7RC2.tgz;
	#tar -xzf gmagick-1.1.7RC2.tgz;
	#cd gmagick-1.1.7RC2;
	#phpize;
	#./configure --with-gmagick=/usr/local;
	#make -j;
	#sudo make install;
	#echo \"extension=gmagick.so\" >> `php --ini | grep \"Loaded Configuration\" | sed -e \"s|.*:\s*||\"`;
	pear config-set preferred_state beta
	printf "\n" | pecl install gmagick
	echo "extension=gmagick.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	php --ri gmagick;
fi

composer self-update
composer install --prefer-source --no-interaction --dev
