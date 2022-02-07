<?php
use Magento\Framework\App\ScopeInterface as IScope;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Framework\UrlInterface as U;
use Magento\Sales\Model\Order as O;
use Magento\Store\Api\Data\StoreInterface as IStore;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManager;
use Magento\Store\Model\StoreManagerInterface as IStoreManager;
use Magento\Store\Model\StoreResolver;

/**
 * 2015-02-04
 * 2015-11-04
 * By analogy with @see \Magento\Store\Model\StoreResolver::getCurrentStoreId()
 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Store/Model/StoreResolver.php#L82
 * 2020-06-24 "Port the `df_store` function": https://github.com/justuno-com/core/issues/122
 * @used-by ju_store_id()
 * @used-by ju_store_url()
 * @used-by ju_url_frontend()
 * @used-by \Justuno\Core\Config\Settings::s()
 * @used-by \Justuno\M2\Store::v()
 * @param int|string|null|bool|IStore|O $v [optional]
 * @return IStore|Store
 * @throws NSE|\Exception
 */
function ju_store($v = null) {/** @var string|null $c */return
	!is_null($v) ? (ju_is_o($v) ? $v->getStore() : (is_object($v) ? $v : ju_store_m()->getStore($v))) :
		ju_store_m()->getStore(!is_null($c = ju_request(StoreResolver::PARAM_NAME)) ? $c : (
			# 2017-08-02
			# The store ID specified in the current URL should have priority over the value from the cookie.
			# Violating this rule led us to the following failure:
			# https://github.com/mage2pro/markdown/issues/1
			# Today I was saving a product in the backend, the URL looked like:
			# https://site.com/admin/catalog/product/save/id/45/type/simple/store/0/set/20/key/<key>/back/edit
			# But at the same time I had another store value in the cookie (a frontend store code).
			!is_null($c = ju_request('store-view')) ? $c : (
				ju_is_backend() ? ju_request('store', 'admin') : (
					!is_null($c = ju_store_cookie_m()->getStoreCodeFromCookie()) ? $c : null
				)
			)
		))
;}

/**
 * 2016-01-30
 * 2020-06-26 "Port the `df_store_code` function": https://github.com/justuno-com/core/issues/155
 * @used-by ju_sentry()
 * @param null|string|int|IScope $s [optional]
 * @return string
 */
function ju_store_code($s = null) {return ju_scope_code($s);}

/**
 * 2016-01-11
 * 2020-08-24 "Port the `df_store_id` function" https://github.com/justuno-com/core/issues/320
 * @used-by ju_product()
 * @param int|string|null|bool|IStore $store [optional]
 * @return int
 */
function ju_store_id($store = null) {return ju_store($store)->getId();}

/**
 * 2017-02-07
 * 2020-06-24 "Port the `df_store_m` function": https://github.com/justuno-com/core/issues/124
 * @used-by ju_store()
 * @used-by \Justuno\M2\Store::v()
 * @return IStoreManager|StoreManager
 */
function ju_store_m() {return ju_o(IStoreManager::class);}

/**
 * 2017-03-15 Returns an empty string if the store's root URL is absent in the Magento database.
 * 2020-06-24 "Port the `df_store_url` function": https://github.com/justuno-com/core/issues/121
 * @used-by ju_store_url_web()
 * @param int|string|null|bool|IStore $s
 * @param string $type
 * @return string
 */
function ju_store_url($s, $type) {return ju_store($s)->getBaseUrl($type);}

/**
 * 2017-03-15 Returns an empty string if the store's root URL is absent in the Magento database.
 * 2020-06-24 "Port the `df_store_url_web` function": https://github.com/justuno-com/core/issues/120
 * @used-by ju_domain_current()
 * @param int|string|null|bool|IStore $s [optional]
 * @return string
 */
function ju_store_url_web($s = null) {return ju_store_url($s, U::URL_TYPE_WEB);}