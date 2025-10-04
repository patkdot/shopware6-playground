#!/bin/bash
ddev delete --yes
rm -rf shopware/vendor
ddev poweroff
ddev start
ddev exec composer install
ddev exec bin/console system:install --basic-setup --shop-locale=de-DE --force
ddev exec bin/console plugin:uninstall SwagPayPal
rm -rf shopware/custom/plugins/SwagPayPal
ddev exec composer require --dev shopware/dev-tools
ddev exec APP_ENV=prod bin/console framework:demodata --orders=0
ddev exec bin/console es:index
echo "Create .env.local ..."
cat <<EOF > shopware/.env.local
APP_DEBUG=1
APP_ENV=dev
EOF
ddev exec bin/console plugin:install -a KdotPlayground
ddev exec bin/console kdot:import
ddev exec bin/console dal:refresh:index
ddev exec bin/console system:config:set core.frw.completedAt "2024-03-08T12:00:00+00:00"