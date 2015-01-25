sudo apt-get remove -y imagemagick libmagickcore-dev libmagickwand-dev;
sudo apt-get autoremove;
sudo apt-get install -y libtiff-dev libjpeg-dev libdjvulibre-dev libwmf-dev pkg-config;

if [ "$IMAGE_DRIVER" = "imagick" ] ; then
	curl -O http://www.imagemagick.org/download/releases/ImageMagick-6.9.0-4.tar.gz;
	tar xzf ImageMagick-6.9.0-4.tar.gz;
	cd ImageMagick-6.9.0-4;
	./configure --prefix=/usr/local/imagemagick --with-libtiff;
	make -j;
	sudo make insall;
	export PKG_CONFIG_PATH=$PKG_CONFIG_PATH:/usr/local/imagemagick/lib/pkgconfig;
	sudo ln -s /usr/local/imagemagick/include/ImageMagick-6 /usr/local/imagemagick/include/ImageMagick;
	cd ..;
	curl -O http://pecl.php.net/get/imagick-3.2.0RC1.tgz;
	tar xzf imagick-3.2.0RC1.tgz;
	cd imagick-3.2.0RC1;
	phpize;
	./configure --with-imagick=/usr/local/imagemagick;
	make -j;
	sudo make install;
	#echo "extension=imagick.so" >> `php --ini | grep "Loaded Configuration" | sed -e "s|.*:\s*||"`;
	echo "extension = imagick.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
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
