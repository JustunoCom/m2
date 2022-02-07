<?php
/**
 * 2016-08-10
 * 2020-08-19 "Port the `df_cc_method` function" https://github.com/justuno-com/core/issues/202
 * @used-by \Justuno\Core\Qa\Trace\Frame::methodName()
 * @param string|object|null|array(object|string)|array(string = string) $a1
 * @param string|null $a2 [optional]
 * @return string
 */
function ju_cc_method($a1, $a2 = null) {return ju_ccc('::',
	$a2 ? [ju_cts($a1), $a2] : (
		!isset($a1['function']) ? $a1 :
			[jua($a1, 'class'), $a1['function']]
	)
);}

/**
 * 2016-01-01
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * 2020-08-22 "Port the `ju_class_f` function" https://github.com/justuno-com/core/issues/264
 * @used-by ju_class_my()
 * @param string|object $c
 * @return string
 */
function ju_class_f($c) {return ju_first(ju_explode_class($c));}

/**
 * 2015-12-29
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * 2020-08-19 "Port the `df_class_l` function" https://github.com/justuno-com/core/issues/199
 * @used-by \Justuno\Core\Qa\Trace\Formatter::frame()
 * @used-by \Justuno\M2\Response::p()
 * @param string|object $c
 * @return string
 */
function ju_class_l($c) {return ju_last(ju_explode_class($c));}

/**
 * 2016-01-01
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * https://3v4l.org/k6Hd5
 * 2020-08-22 "Port the `df_class_my` function" https://github.com/justuno-com/core/issues/263
 * @used-by \Justuno\Core\Config\Plugin\Model\Config\SourceFactory::aroundCreate()
 * @param string|object $c
 * @return bool
 */
function ju_class_my($c) {return in_array(ju_class_f($c), ['Justuno']);}

/**
 * 2015-08-14 @uses get_class() does not add the leading slash `\` before the class name: http://3v4l.org/HPF9R
 * 2015-09-01
 * @uses ltrim() correctly handles Cyrillic letters: https://3v4l.org/rrNL9
 * 		echo ltrim('\\Путь\\Путь\\Путь', '\\');  => Путь\Путь\Путь
 * 2016-10-20 $c is required here because it is used by @used-by get_class(): https://3v4l.org/k6Hd5
 * 2020-06-26 "Port the `df_cts` function": https://github.com/justuno-com/core/issues/141
 * @used-by ju_cc_method()
 * @used-by ju_explode_class()
 * @used-by ju_explode_class_camel()
 * @used-by ju_fe_init()
 * @used-by ju_module_name()
 * @param string|object $c
 * @param string $del [optional]
 * @return string
 */
function ju_cts($c, $del = '\\') {/** @var string $r */
	$r = ju_trim_text_right(is_object($c) ? get_class($c) : ltrim($c, '\\'), '\Interceptor');
	return '\\' === $del ? $r : str_replace('\\', $del, $r);
}

/**
 * 2020-06-26 "Port the `df_explode_class` function": https://github.com/justuno-com/core/issues/139
 * @used-by ju_class_f()
 * @used-by ju_class_l()
 * @used-by ju_explode_class_lc()
 * @used-by ju_module_name()
 * @param string|object $c
 * @return string[]
 */
function ju_explode_class($c) {return ju_explode_multiple(['\\', '_'], ju_cts($c));}

/**
 * 2016-04-11 Dfe_CheckoutCom => [Dfe, Checkout, Com]
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * 2020-08-21 "Port the `df_explode_class_camel` function" https://github.com/justuno-com/core/issues/220
 * @used-by ju_explode_class_lc_camel()
 * @param string|object $c
 * @return string[]
 */
function ju_explode_class_camel($c) {return jua_flatten(ju_explode_camel(explode('\\', ju_cts($c))));}

/**
 * 2016-01-14
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * 2020-08-22 "Port the `ju_explode_class_lc` function" https://github.com/justuno-com/core/issues/243
 * @param string|object $c
 * @return string[]
 */
function ju_explode_class_lc($c) {return ju_lcfirst(ju_explode_class($c));}

/**
 * 2016-04-11
 * 2016-10-20
 * 1) Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * 2) Dfe_CheckoutCom => [dfe, checkout, com]
 * 2020-08-21 "Port the `df_explode_class_lc_camel` function" https://github.com/justuno-com/core/issues/217
 * @used-by ju_module_name_lc()
 * @param string|object $c
 * @return string[]
 */
function ju_explode_class_lc_camel($c) {return ju_lcfirst(ju_explode_class_camel($c));}

/**
 * 2021-02-24
 * @used-by ju_caller_c()
 * @param string $m
 * @return string[]
 */
function ju_explode_method($m) {return explode('::', $m);}