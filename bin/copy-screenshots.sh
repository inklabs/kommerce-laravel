#!/bin/bash

cp vendor/inklabs/kommerce-templates/codeception/_output/debug/*.png docs/screenshots/
optipng docs/screenshots/*.png
