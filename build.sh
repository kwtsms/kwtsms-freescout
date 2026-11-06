#!/bin/sh
# Builds dist/kwtsms-freescout-<version>.zip: the module folder "KwtSms" with the kwtSMS PHP library in vendor/.
# Usage: sh build.sh
set -eu
VERSION=$(python3 -c "import json;print(json.load(open('module.json'))['version'])")
rm -rf build dist && mkdir -p build/KwtSms dist
cp -r module.json start.php composer.json LICENSE Config Providers Services Resources build/KwtSms/
(cd build/KwtSms && composer install -q --no-dev --no-interaction --optimize-autoloader && rm composer.lock \
    && find vendor/kwtsms/kwtsms-php -mindepth 1 -maxdepth 1 ! -name src ! -name LICENSE ! -name composer.json -exec rm -rf {} +)
(cd build && zip -qr ../dist/kwtsms-freescout-$VERSION.zip KwtSms)
rm -rf build
echo "dist/kwtsms-freescout-$VERSION.zip"
