sudo apt-get remove -y imagemagick libmagickcore-dev libmagickwand-dev;
sudo apt-get autoremove;
sudo apt-get install -y libtiff-dev libjpeg-dev libdjvulibre-dev libwmf-dev pkg-config;

if [ "$IMAGE_DRIVER" = "imagick" ] ; then
	sudo apt-get install -y imagemagick;
	pear config-set preferred_state beta;
	printf "\n" | pecl install imagick-3.2.0RC1;
	echo "extension=imagick.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`;
	php --ri imagick;
fi

if [ "$IMAGE_DRIVER" = "gmagick" ] ; then
	sudo apt-get install -y graphicsmagick libgraphicsmagick1-dev;
	pear config-set preferred_state beta;
	printf "\n" | pecl install gmagick-1.1.7RC2;
	echo "extension=gmagick.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`;
	php --ri gmagick;
fi

composer self-update
composer -vvv install --prefer-source --no-interaction --dev
