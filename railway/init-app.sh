#!/bin/sh
# Pré-deploy no Railway (Settings > Deploy > Pre-Deploy Command):
#   chmod +x ./railway/init-app.sh && sh ./railway/init-app.sh
# NÃO roda migrations: o banco é criado pelo database/sql/script_deploy.sql.
set -e
php artisan storage:link --force || true
php artisan config:cache
php artisan route:cache
php artisan view:cache
