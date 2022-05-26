#!/usr/bin/env bash

set -e

if [[ ! -e ./public/build/manifest.json ]]; then
    mkdir -p ./public/build
    echo "{}" > ./public/build/manifest.json
fi

composer install --dev --prefer-dist --no-interaction --no-scripts --no-progress --no-suggest
vendor/bin/simple-phpunit -c ./phpunit.xml.dist