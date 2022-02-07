<?php
use Magento\Framework\App\Config;
use Magento\Framework\App\Config\Data as ConfigData;
use Magento\Framework\App\Config\DataInterface as IConfigData;
use Magento\Framework\App\Config\ScopeConfigInterface as IConfig;
use Magento\Framework\App\ScopeInterface as ScopeA;
use Magento\Store\Model\ScopeInterface as SS;
use Magento\Store\Model\Store;

/**
 * @uses \Magento\Framework\App\Config\Data::getValue()
 * https://github.com/magento/magento2/blob/2335247d4ae2dc1e0728ee73022b0a244ccd7f4c/lib/internal/Magento/Framework/App/Config/Data.php#L47-L62
 *
 * 2015-12-26
 * https://mage2.pro/t/357
 * «The @uses \Magento\Framework\App\Config::getValue() method
 * has a wrong PHPDoc type for the $scopeCode parameter».
 *
 * Метод возвращает null или $default, если данные отсутствуют:
 * @see \Magento\Framework\App\Config\Data::getValue()
 * https://github.com/magento/magento2/blob/6ce74b2/lib/internal/Magento/Framework/App/Config/Data.php#L47-L62
 *
 * 2015-10-09
 * https://mage2.pro/t/128
 * https://github.com/magento/magento2/issues/2064
 *
 * 2016-12-15
 * Добавил возможность передачи в качестве $scope массива из 2-х элементов: [Scope Type, Scope Code].
 * Это стало ответом на удаление из ядра класса \Magento\Framework\App\Config\ScopePool
 * в Magento CE 2.1.3: https://github.com/magento/magento2/commit/3660d012
 *
 * 2017-10-22
 * The @see \Magento\Store\Model\ScopeInterface::SCOPE_STORE constant exists in all the Magento 2 versions:
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/ScopeInterface.php#L17
 *
 * 2020-08-23 "Port the `ju_cfg` function" https://github.com/justuno-com/core/issues/292
 *
 * @used-by ju_currency_base()
 * @used-by ju_mail()
 * @used-by \Justuno\Core\Config\Settings::v()
 * @used-by \Justuno\M2\Filter::byDate()
 * @used-by \Justuno\M2\Response::p()
 * @param string|string[] $k
 * @param null|string|int|ScopeA|Store|IConfigData|ConfigData|array(int|string) $scope [optional]
 * @param mixed|callable $d [optional]
 * @return array|string|null|mixed
 */
function ju_cfg($k, $scope = null, $d = null) {
	if (is_array($k)) {
		$k = ju_cc_path($k);
	}
	/** @var array|string|null|mixed $r */
	$r = $scope instanceof IConfigData ? $scope->getValue($k) : ju_cfg_m()->getValue($k, ...(
		is_array($scope) ? [$scope[0], $scope[1]] : [SS::SCOPE_STORE, $scope])
	);
	return ju_if(ju_cfg_empty($r), $d, $r);
}

/**
 * 2016-11-12
 * 2020-08-23 "Port the `df_cfg_empty` function" https://github.com/justuno-com/core/issues/295
 * @used-by ju_cfg()
 * @param array|string|null|mixed $v
 * @return bool
 */
function ju_cfg_empty($v) {return is_null($v) || '' === $v;}

/**
 * 2016-02-09
 * https://mage2.pro/t/639
 * The default implementation of the @see \Magento\Framework\App\Config\ScopeConfigInterface
 * is @see \Magento\Framework\App\Config
 * 2020-08-23 "Port the `ju_cfg_m` function" https://github.com/justuno-com/core/issues/293
 * @used-by ju_cache_clean()
 * @used-by ju_cfg()
 * @return IConfig|Config
 */
function ju_cfg_m() {return ju_o(IConfig::class);}