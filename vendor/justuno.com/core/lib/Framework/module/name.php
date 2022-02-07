<?php
/**
 * «Dfe\AllPay\W\Handler» => «Dfe_AllPay»
 *
 * 2016-10-26
 * The function correctly handles class names without a namespace and with the `_` character:
 * «A\B\C» => «A_B»
 * «A_B» => «A_B»
 * «A» => A»
 * https://3v4l.org/Jstvc
 *
 * 2017-01-27
 * $c could be:
 * 1) a module name: «A_B»
 * 2) a class name: «A\B\C».
 * 3) an object: it comes down to the case 2 via @see get_class()
 * 4) `null`: it comes down to the case 1 with the «Justuno_Core» module name.
 *
 * 2020-06-26 "Port the `ju_module_name` function": https://github.com/justuno-com/core/issues/138
 *
 * @used-by ju_asset_name()
 * @used-by ju_fe_init()
 * @used-by ju_js_x()
 * @used-by ju_module_dir()
 * @used-by ju_module_name_c()
 * @used-by ju_package()
 * @used-by ju_sentry_module()
 * @used-by \Justuno\M2\Plugin\Framework\App\Router\ActionList::aroundGet()
 * @param string|object|null $c [optional]
 * @param string $del [optional]
 * @return string
 */
function ju_module_name($c = null, $del = '_') {return jucf(function($c, $del) {return implode($del, array_slice(
	ju_explode_class($c), 0, 2
));}, [$c ? ju_cts($c) : 'Justuno\Core', $del]);}

/**
 * 2017-01-04
 * $c could be:
 * 1) a module name. E.g.: «A_B».
 * 2) a class name. E.g.: «A\B\C».
 * 3) an object. It will be treated as case 2 after @see get_class()
 * 2020-08-21 "Port the `df_module_name_c` function" https://github.com/justuno-com/core/issues/218
 * @used-by ju_module_name_lc()
 * @param string|object|null $c [optional]
 * @return string
 */
function ju_module_name_c($c = null) {return ju_module_name($c, '\\');}

/**
 * 2016-02-16 «Dfe\CheckoutCom\Method» => «dfe_checkout_com»
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * 2017-10-03
 * $c could be:
 * 1) a module name. E.g.: «A_B».
 * 2) a class name. E.g.: «A\B\C».
 * 3) an object. It will be treated as case 2 after @see get_class()
 * 2020-08-21 "Port the `df_module_name_lc` function" https://github.com/justuno-com/core/issues/216
 * @used-by ju_report_prefix()
 * @used-by \Justuno\Core\Exception::reportNamePrefix()
 * @param string|object $c
 * @param string $del [optional]
 * @return string
 */
function ju_module_name_lc($c, $del = '_') {return implode($del, ju_explode_class_lc_camel(ju_module_name_c($c)));}