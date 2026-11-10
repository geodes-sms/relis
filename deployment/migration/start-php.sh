#!/bin/sh
set -eu
cd /u/relis/public_html
test -n "${RELIS_MIGRATION_ID:-}"
test -f vendor/autoload.php
mkdir -p relis_app/logs relis_app/libraries/table_config/project/temp \
  cside/sessions cside/export_r cside/export_python cside/media/images /migration
chown -R www-data:www-data relis_app/config relis_app/logs \
  relis_app/libraries/table_config/project cside/sessions cside/export_r \
  cside/export_python cside/media /migration
exec apache2-foreground
