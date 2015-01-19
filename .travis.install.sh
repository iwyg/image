set -x
if [ "$TRAVIS_PHP_VERSION" = 'hhvm' ] || [ "$TRAVIS_PHP_VERSION" = 'hhvm-nightly' ] ; then
    echo "skipping imagick installation"
else
	pear config-set preferred_state beta
	printf "\n" | pecl install imagick
	echo "extension = imagick.so" >> ~/.phpenv/versions/$(phpenv version-name)/etc/php.ini
	php -m | grep imagick 
fi

composer self-update
composer install --prefer-source --no-interaction --dev
