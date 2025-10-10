# Shopware 6 Playground

## Setup fresh environment and delete old one

* Install ddev: https://docs.ddev.com/en/stable/users/install/ddev-installation/
* Clone repository
* In repository root run:
```bash
chmod +x setup.sh && ./setup.sh
```

Backend: https://shopware6-playground.ddev.site/admin#/login/ \
Frontend: https://shopware6-playground.ddev.site/ \
For more info run: `ddev describe`


## What it contains

* Customer specific "bought" badge on listing page via Javascript plugin
* Adds product extension with translations
* Command to import product extension data
    * via raw sql or DAL
    * sync service
    * message queue or direct
* Customfield installer
* Database migration
* Adds new product extension data to template via subscriber and criteria
* API Route for new product extension data entity
* PHPUnit tests


## Code Quality

* `ddev phpstan`
* `ddev phpunit`
* `ddev phpcs`
* Everything combined: `ddev qs`

