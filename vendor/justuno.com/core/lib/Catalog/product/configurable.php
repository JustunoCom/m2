<?php
use Magento\Catalog\Model\Product as P;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as T;

/**
 * 2016-05-01 How to programmatically detect whether a product is configurable? https://mage2.pro/t/1501
 * 2020-08-24 "Port the `df_configurable` function" https://github.com/justuno-com/core/issues/314
 * @used-by \Justuno\M2\Catalog\Variants::p()
 * @used-by \Justuno\M2\Controller\Cart\Add::execute()
 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
 * @used-by \Justuno\M2\Inventory\Variants::p()
 * @param P $p
 * @return bool
 */
function ju_configurable(P $p) {return T::TYPE_CODE === $p->getTypeId();}