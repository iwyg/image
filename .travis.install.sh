set -x
sudo apt-get remove -y imagemagick libmagickcore-dev libmagickwand-dev
sudo apt-get autoremove
sudo apt-get install -y libtiff-dev libjpeg-dev libdjvulibre-dev libwmf-dev pkg-config

if ["$IMAGE_DRIVER" = 'imagick'] ; then
	pear config-set preferred_state beta
	printf "\n" | pecl install imagick
	echo "extension = imagick.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	php --ri imagick 
fi

if ["$IMAGE_DRIVER" = 'gmagick'] ; then
	pear config-set preferred_state beta
	printf "\n" | pecl install gmagick
	echo "extension=gmagick.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	php --ri gmagick;
fi

composer self-update
composer install --prefer-source --no-interaction --dev
