#!/usr/bin/env bash

set -e
set -o pipefail

phpunit
Tests/Functional/app/console behat
