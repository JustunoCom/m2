<?php
use Justuno\Core\Exception as DFE;
/**
 * 2015-09-02
 * 2017-03-15 @uses \Magento\Framework\App\Request\Http::getFullActionName() returns «__» in the CLI case.
 * 2020-08-24 "Port the `df_action_name` function" https://github.com/justuno-com/core/issues/304
 * 2022-02-23
 * The function returns «__» is the  following methods are not yet called by Magento:
 * @see \Magento\Framework\App\Request\Http::setRouteName()
 * @see \Magento\Framework\HTTP\PhpEnvironment\Request::setActionName()
 * @see \Magento\Framework\HTTP\PhpEnvironment\Request::setControllerName()
 * In this case, use `ju_request_o()->getPathInfo()`: @see ju_rp_has()
 * @used-by ju_action_prefix()
 * @used-by \Justuno\M2\Block\Js::_toHtml()
 * @return string|null
 * @throws DFE
 */
function ju_action_name() {return ju_is_cli() ? null : ju_assert_ne('__', ju_request_o()->getFullActionName(),
	'`Magento\Framework\App\Request\Http::getFullActionName()` is called to early (the underlying object is not yet initialized).'
);}

/**
 * 2017-08-28
 * 2021-03-06 "Port the `df_action_prefix` function": https://github.com/justuno-com/core/issues/359
 * @used-by ju_is_system_config()
 * @param string|string[] $p
 * @return bool
 */
function ju_action_prefix($p) {return ju_starts_with(ju_action_name(), $p);}

/**
 * 2019-12-26
 * 2020-08-21 "Port the `df_referer` function" https://github.com/justuno-com/core/issues/211
 * @see \Magento\Store\App\Response\Redirect::getRefererUrl():
 * 		df_response_redirect()->getRefererUrl()
 * @used-by ju_log_l()
 * @used-by https://github.com/royalwholesalecandy/core/issues/58#issuecomment-569049731
 * @return string
 */
function ju_referer() {return jua($_SERVER, 'HTTP_REFERER');}