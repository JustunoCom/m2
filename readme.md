[Justuno](https://www.justuno.com) module for Magento 2. 

## How to install
```
bin/magento maintenance:enable
rm -rf app/code/Justuno/Jumagext
rm -rf composer.lock
composer clear-cache
composer require justuno.com/m2:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/*
bin/magento setup:di:compile
bin/magento cache:enable
rm -rf pub/static/*
bin/magento setup:static-content:deploy -f en_US <additional locales>
bin/magento maintenance:disable
```

## How to upgrade
```
bin/magento maintenance:enable
composer remove justuno.com/m2
rm -rf composer.lock
composer clear-cache
composer require justuno.com/m2:*
bin/magento setup:upgrade
bin/magento cache:enable
rm -rf var/di var/generation generated/*
bin/magento setup:di:compile
bin/magento cache:enable
rm -rf pub/static/*
bin/magento setup:static-content:deploy -f en_US <additional locales>
bin/magento maintenance:disable
```

<h2 id='doc'>Documentation</h2>

- [Where are Justuno settings located in the Magento’s backend?](https://github.com/JustunoCom/m2/blob/master/doc/settings.md#h)
- [Where to find my «Justuno Account Number»?](https://github.com/JustunoCom/m2/blob/master/doc/account-number.md#h)
- [How to provide the developer with the database access?](https://github.com/JustunoCom/m2/blob/master/doc/database-access.md#h)