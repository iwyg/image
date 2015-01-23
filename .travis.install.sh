set -x
sudo apt-get remove -y imagemagick libmagickcore-dev libmagickwand-dev
sudo apt-get autoremove
sudo apt-get install -y libtiff-dev libjpeg-dev libdjvulibre-dev libwmf-dev pkg-config

if [ "$IMAGE_DRIVER" = "imagick" ] ; then
	pear config-set preferred_state beta
	printf "\n" | sudo pecl install imagick-3.2.0RC1
	echo "extension = imagick.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	php --ri imagick 
fi

if [ "$IMAGE_DRIVER" = "gmagick" ] ; then
	pear config-set preferred_state beta
	printf "\n" | sudo pecl install gmagick-1.1.7RC2
	echo "extension=gmagick.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	php --ri gmagick;
fi

composer self-update
composer install --prefer-source --no-interaction --dev
