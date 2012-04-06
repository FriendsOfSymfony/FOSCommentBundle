#!/usr/bin/env bash

set -e
set -o pipefail

phpunit

Tests/Functional/app/console assets:install --symlink Tests/Functional/web
Tests/Functional/app/console doctrine:database:drop --force
Tests/Functional/app/console doctrine:database:create
Tests/Functional/app/console doctrine:schema:create

Tests/Functional/app/console behat
