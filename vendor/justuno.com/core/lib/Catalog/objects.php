<?php
use Justuno\Core\Exception as DFE;
use Magento\Catalog\Model\Locator\LocatorInterface as ILocator;
use Magento\Catalog\Model\Locator\RegistryLocator;

/**
 * 2016-02-25 https://github.com/magento/magento2/blob/e0ed4bad/app/code/Magento/Catalog/etc/adminhtml/di.xml#L10-L10
 * 2020-08-24 "Port the `df_catalog_locator` function" https://github.com/justuno-com/core/issues/307
 * @used-by ju_product_current()
 * @return ILocator|RegistryLocator
 * @throws DFE
 */
function ju_catalog_locator() {
	ju_assert(ju_is_backend()); # 2019-08-01 Locator is available only in backend.
	return ju_o(ILocator::class);
}