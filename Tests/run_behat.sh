#!/usr/bin/env sh

Functional/app/console assets:install --symlink Functional/web
Functional/app/console doctrine:database:create
Functional/app/console doctrine:schema:create
Functional/app/console behat