#!/usr/bin/env bash

set -e
set -o pipefail

phpunit --coverage-text --colors
Tests/Functional/app/console behat --format=progress
