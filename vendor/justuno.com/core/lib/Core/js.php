<?php
/**
 * 2015-10-26 https://mage2.pro/t/145
 * 2016-11-28
 * An example:
 * https://github.com/magento/magento2/blob/2.1.2/app/code/Magento/Theme/view/frontend/templates/js/cookie.phtml#L16-L26
 * Such syntax (unlike @see df_widget() ) does not allow us to pass a DOM element as the second argument:
 * https://github.com/magento/magento2/blob/2.1.2/lib/web/mage/apply/main.js#L69-L70
 * 2017-04-21
 * 1) This function does not associate the JavaScript code with any DOM element.
 * If you want such association, then use @see df_widget() instead.
 * 2) $m could be:
 * 2.1) A module name: «A_B»
 * 2.2) A class name: «A\B\C».
 * 2.3) An object: it comes down to the case 2 via @see get_class()
 * 2.4) 2017-10-16: `null`, if $script is an absolute URL.
 * 2020-08-22 "Port the `df_js` function" https://github.com/justuno-com/core/issues/246
 * @used-by ju_fe_init()
 * @used-by \Justuno\M2\Block\Js::_toHtml()
 * @param string|object|null $m
 * @param string|null $s [optional]
 * @param array(string => mixed) $p [optional]
 * @return string
 */
function ju_js($m, $s = null, array $p = []) {$s = $s ?: 'main'; return ju_js_x(
	'*', ju_check_url_absolute($s) ? null : $m, $s, $p
);}

/**
 * 2019-06-01
 * 2020-08-22 "Port the `df_js_x` function" https://github.com/justuno-com/core/issues/252
 * @used-by ju_js()
 * @param string $selector
 * @param string|object|null $m
 * $m could be:
 * 1) A module name: «A_B»
 * 2) A class name: «A\B\C».
 * 3) An object: it comes down to the case 2 via @see get_class()
 * 4) `null`.
 * @param string|null $s [optional]
 * @param array(string => mixed) $p [optional]
 * @return string
 */
function ju_js_x($selector, $m, $s = null, array $p = []) {return ju_tag(
	'script', ['type' => 'text/x-magento-init'], ju_json_encode([$selector => [
		ju_cc_path(is_null($m) ? null : ju_module_name($m), $s ?: 'main') => $p
	]])
);}