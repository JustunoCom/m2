<?php
use Magento\Directory\Model\Currency as C;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Store\Model\Store;

/**
 * 2016-09-05
 * 2020-08-26 "Port the `df_currency_convert_from_base` function" https://github.com/justuno-com/core/issues/336
 * @used-by ju_oqi_price()
 * @param float $a
 * @param C|string|null $to
 * @param null|string|int|ScopeA|Store|ConfigData|IConfigData $s [optional]
 * @return float
 */
function ju_currency_convert_from_base($a, $to, $s = null) {return ju_currency_base($s)->convert($a, $to);}