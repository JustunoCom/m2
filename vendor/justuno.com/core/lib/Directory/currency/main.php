<?php
use Magento\Directory\Model\Currency as C;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Quote\Model\Quote as Q;
use Magento\Sales\Model\Order as O;
use Magento\Store\Model\Store;

/**
 * 2016-07-04 «How to load a currency by its ISO code?» https://mage2.pro/t/1840
 * 2020-08-26 "Port the `df_currency` function" https://github.com/justuno-com/core/issues/342
 * @used-by ju_currency_base()
 * @param C|string|null $c [optional]
 * @return C
 */
function ju_currency($c = null) {/** @var C $r */
	if (!$c) {
		$r = ju_currency_base();
	}
	elseif ($c instanceof C) {
		$r = $c;
	}
	else {
		static $cache; /** @var array(string => Currency) $cache */
		if (!isset($cache[$c])) {
			$cache[$c] = ju_new_om(C::class)->load($c);
		}
		$r = $cache[$c];
	}
	return $r;
}

/**
 * 2016-07-04 «How to programmatically get the base currency's ISO code for a store?» https://mage2.pro/t/1841
 * 2016-12-15
 * Добавил возможность передачи в качестве $scope массива из 2-х элементов: [Scope Type, Scope Code].
 * Это стало ответом на удаление из ядра класса \Magento\Framework\App\Config\ScopePool
 * в Magento CE 2.1.3: https://github.com/magento/magento2/commit/3660d012
 * 2020-08-26 "Port the `df_currency_base` function" https://github.com/justuno-com/core/issues/341
 * @used-by ju_currency()
 * @used-by ju_currency_convert_from_base()
 * @param ScopeA|Store|ConfigData|IConfigData|O|Q|array(int|string)|null|string|int $s [optional]
 * @return C
 */
function ju_currency_base($s = null) {return ju_currency(ju_assert_sne(ju_cfg(
	C::XML_PATH_CURRENCY_BASE, ju_is_oq($s) ? $s->getStore() : $s
)));}