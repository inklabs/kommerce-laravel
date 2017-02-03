#!/bin/bash

cd /var/www/html

# composer install --no-interaction --no-dev --optimize-autoloader >> /dev/stdout 2>&1

if [ -f "composer.json" ]; then
    # run the composer scripts for post-deploy
    if su www-data -c "php composer --no-ansi run-script -l" \
        | grep -q "post-deploy-cmd"; then
        su www-data -c \
            "php composer run-script post-deploy-cmd \
            --no-ansi \
            --no-interaction" \
            || (echo 'Failed to execute post-deploy-cmd'; exit 1)
    fi
fi

/usr/bin/supervisord -n -c /etc/supervisord.conf

