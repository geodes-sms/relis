#!/bin/sh
set -eu
cd "${APACHE_DOCUMENT_ROOT:-/u/relis/public_html}"
if [ ! -f relis_app/config/database.php ]; then
    cp relis_app/config/database.example.php relis_app/config/database.php
fi
if [ ! -f vendor/autoload.php ]; then
    echo 'Missing Composer dependencies. Install the lockfile before starting ReLiS.' >&2
    exit 1
fi
mkdir -p cside/export_r cside/export_python cside/sessions relis_app/logs \
    relis_app/libraries/table_config/project/temp
chown -R www-data:www-data cside/export_r cside/export_python cside/sessions \
    relis_app/logs relis_app/config relis_app/libraries/table_config/project
exec apache2-foreground