<?php
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website as W;
/**
 * 2019-11-22
 * The $v argument could be one of:
 * *) a website: W
 * *) a store: Store
 * *) a website's ID: int
 * *) a website's code: string
 * *) null or absert: the current website
 * *) true: the default website
 * 2020-08-23 "Port the `df_website` function" https://github.com/justuno-com/core/issues/286
 * @used-by ju_website_code()
 * @param W|Store|int|string|null|bool $v [optional]
 * @return W
 * @throws NSE|\Exception
 */
function ju_website($v = null) {return $v instanceof Store ? $v->getWebsite() : ju_store_m()->getWebsite($v);}

/**
 * 2019-11-22
 * The $v argument could be one of:
 * *) a website: W
 * *) a store: Store
 * *) a website's ID: int
 * *) a website's code: string
 * *) null or absert: the current website
 * *) true: the default website
 * 2020-08-23 "Port the `df_website_code` function" https://github.com/justuno-com/core/issues/285
 * @used-by ju_msi_website2stockId()
 * @param W|Store|int|string|null|bool $v [optional]
 * @return string
 * @throws Exception
 * @throws NSE
 */
function ju_website_code($v = null) {return ju_website($v)->getCode();}
