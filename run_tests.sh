#!/usr/bin/env bash

set -e
set -o pipefail

phpunit --coverage-text --colors

echo ""
echo "==============================="
echo "BEHAT:"
echo ""

 
Tests/Functional/app/console behat --format=progress
