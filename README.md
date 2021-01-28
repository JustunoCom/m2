[Justuno](https://www.justuno.com) module for Magento 2. 

## How to install
```
bin/magento maintenance:enable
rm -rf app/code/Justuno/Jumagext
rm -rf composer.lock
composer clear-cache
composer require justuno.com/m2:*
bin/magento setup:upgrade
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
rm -rf var/di var/generation generated/*
bin/magento setup:di:compile
bin/magento cache:enable
rm -rf pub/static/*
bin/magento setup:static-content:deploy -f en_US <additional locales>
bin/magento maintenance:disable
```

<h2 id="account-number">Where to find my «Justuno Account Number»?</h2>

![](https://mage2.pro/uploads/default/original/2X/4/429d007f47381d01e5eb2d33d762d77fd2e04932.png)  
![](https://mage2.pro/uploads/default/original/2X/3/3ef7cd3ad314c5e2e105f56154385bbe9be0f617.png)