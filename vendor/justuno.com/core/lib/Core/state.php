<?php
use Magento\Framework\App\ProductMetadata;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\State;
/**
 * 2015-09-20
 * 2020-06-24 "Port the `df_app_state` function": https://github.com/justuno-com/core/issues/128
 * @used-by ju_area_code()
 * @return State
 */
function ju_app_state() {return ju_o(State::class);}

/**
 * http://mage2.ru/t/37
 * 2020-08-21 "Port the `ju_current_url` function" https://github.com/justuno-com/core/issues/212
 * @used-by ju_log_l()
 * @return string
 */
function ju_current_url() {return ju_url_o()->getCurrentUrl();}

/**
 * https://mage2.ru/t/94
 * https://mage2.pro/t/59
 * 2020-06-24 "Port the `df_is_ajax` function": https://github.com/justuno-com/core/issues/129
 * @used-by ju_is_backend()
 * @used-by ju_is_frontend()
 * @return bool
 */
function ju_is_ajax() {static $r; return !is_null($r) ? $r : $r = ju_request_o()->isXmlHttpRequest();}

/**
 * 2016-05-15 http://stackoverflow.com/a/2053295
 * 2017-06-09 It intentionally returns false in the CLI mode.
 * 2020-08-14 "Port the `df_is_localhost` function" https://github.com/justuno-com/core/issues/186
 * @used-by ju_my_local()
 * @return bool
 */
function ju_is_localhost() {return in_array(jua($_SERVER, 'REMOTE_ADDR', []), ['127.0.0.1', '::1']);}

/**
 * 2016-06-25 https://mage2.pro/t/543
 * 2018-04-17
 * 1) «Magento 2.3 has removed its version information from the `composer.json` files since 2018-04-05»:
 * https://mage2.pro/t/5480
 * 2) Now Magento 2.3 (installed with Git) returns the «dev-2.3-develop» string from the
 * @see \Magento\Framework\App\ProductMetadata::getVersion() method.
 * 2020-06-26 "Port the `df_magento_version` function": https://github.com/justuno-com/core/issues/153
 * @used-by ju_log_l()
 * @used-by ju_sentry()
 * @used-by ju_sentry_m()
 * @return string
 */
function ju_magento_version() {return jucf(function() {return ju_trim_text_left(
	ju_magento_version_m()->getVersion()
, 'dev-');});}

/**
 * 2016-06-25
 * 2020-06-26 "Port the `df_magento_version_m` function": https://github.com/justuno-com/core/issues/154
 * @used-by ju_magento_version()
 * @return ProductMetadata|ProductMetadataInterface
 */
function ju_magento_version_m() {return ju_o(ProductMetadataInterface::class);}

/**
 * 2017-04-17
 * @used-by ju_my_local()
 * @return bool
 */
function ju_my() {return isset($_SERVER['DF_DEVELOPER']);}

/**
 * 2017-06-09 «dfediuk» is the CLI user name on my localhost.
 * 2020-08-14 "Port the `df_my_local` function" https://github.com/justuno-com/core/issues/184
 * @used-by ju_visitor_ip()
 * @used-by \Justuno\M2\Store::v()
 * @return bool
 */
function ju_my_local() {return jucf(function() {return
	ju_my() && (ju_is_localhost() || 'dfediuk' === jua($_SERVER, 'USERNAME'))
;});}