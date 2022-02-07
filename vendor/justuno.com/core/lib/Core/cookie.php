<?php
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\Cookie\PhpCookieManager;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Stdlib\Cookie\PublicCookieMetadata;
use Magento\Store\Model\StoreCookieManager;
use Magento\Store\Api\StoreCookieManagerInterface;

/**
 * 2015-11-04
 * 2020-06-24 "Port the `df_store_cookie_m` function": https://github.com/justuno-com/core/issues/132
 * @used-by ju_store()
 * @return StoreCookieManagerInterface|StoreCookieManager
 */
function ju_store_cookie_m() {return ju_o(StoreCookieManagerInterface::class);}