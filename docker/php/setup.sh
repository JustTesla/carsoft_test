composer install --classmap-authoritative --no-dev --no-interaction

bin/console doctrine:database:create --no-interaction --if-not-exists
bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
bin/console importmap:install
bin/console asset-map:compile

chown -R www-data: var

php-fpm -F