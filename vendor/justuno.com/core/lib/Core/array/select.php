<?php
use Closure as F;
use Magento\Config\Model\Config\Structure\AbstractElement as AE;
use Magento\Framework\DataObject as _DO;
use Traversable as T;

/**
 * 2020-06-13 "Port the `dfa` function": https://github.com/justuno-com/core/issues/12
 * @used-by ju_asset_create()
 * @used-by ju_block()
 * @used-by ju_call()
 * @used-by ju_caller_m()
 * @used-by ju_cc_method()
 * @used-by ju_cli_argv()
 * @used-by ju_deployment_cfg()
 * @used-by ju_extend()
 * @used-by ju_is_localhost()
 * @used-by ju_my_local()
 * @used-by ju_package()
 * @used-by ju_prop()
 * @used-by ju_referer()
 * @used-by ju_request()
 * @used-by ju_sentry()
 * @used-by ju_sentry_m()
 * @used-by jutr()
 * @used-by \Justuno\Core\Helper\Text::quote()
 * @used-by \Justuno\Core\O::a()
 * @used-by \Justuno\Core\Qa\Trace\Frame::methodParameter()
 * @used-by \Justuno\Core\Sentry\Client::__construct()
 * @used-by \Justuno\Core\Sentry\Client::capture()
 * @used-by \Justuno\Core\Sentry\Client::captureException()
 * @used-by \Justuno\Core\Sentry\Client::needSkipFrame()
 * @used-by \Justuno\Core\Sentry\Trace::get_default_context()
 * @used-by \Justuno\Core\Sentry\Trace::get_frame_context()
 * @used-by \Justuno\Core\Sentry\Trace::info()
 * @used-by \Justuno\Core\Zf\Validate::cfg()
 * @used-by \Justuno\M2\Store::v()
 * @param array(int|string => mixed) $a
 * @param string|string[]|int|null $k
 * @param mixed|callable $d
 * @return mixed|null|array(string => mixed)
 */
function jua(array $a, $k, $d = null) {return
	is_null($k) ? $a : (is_array($k) ? jua_select_ordered($a, $k) : (isset($a[$k]) ? $a[$k] : (
		ju_contains($k, '/') ? jua_deep($a, $k, $d) : ju_call_if($d, $k)
	)))
;}

/**
 * 2020-01-29
 * 2020-06-18 "Port the `dfad` function": https://github.com/justuno-com/core/issues/73
 * @used-by ju_call()
 * @param _DO|AE $o
 * @param string|string[]|null $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return _DO|AE|mixed
 */
function juad($o, $k = null, $d = null) {return is_null($k) ? $o : jua(ju_gd($o), $k, $d);}

/**
 * 2020-06-13 "Port the `jua_select_ordered` function": https://github.com/justuno-com/core/issues/13
 * 1) It returns a subset of $a with $k keys in the same order as in $k.
 * 2) Normally, you should use @see jua() instead because it is shorter and calls jua_select_ordered() internally.
 * @used-by jua()
 * @param array(string => string)|T $a
 * @param string[] $k
 * @return array(string => string)
 */
function jua_select_ordered($a, array $k)  {
	$resultKeys = array_fill_keys($k, null); /** @var array(string => null) $resultKeys */
	/**
	 * 2017-10-28
	 * During the last 2.5 years, I had the following code here:
	 * 		array_merge($resultKeys, df_ita($source))
	 * It worked wronly, if $source contained SOME numeric-string keys like "99":
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
	 *
	 * «A key may be either an integer or a string.
	 * If a key is the standard representation of an integer, it will be interpreted as such
	 * (i.e. "8" will be interpreted as 8, while "08" will be interpreted as "08").»
	 * https://php.net/manual/language.types.array.php
	 *
	 * «If, however, the arrays contain numeric keys, the later value will not overwrite the original value,
	 * but will be appended.
	 * Values in the input array with numeric keys will be renumbered
	 * with incrementing keys starting from zero in the result array.»
	 * https://php.net/manual/function.array-merge.php
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140297
	 * `df_ita($source) + $resultKeys` does not solve the problem,
	 * because the result keys are ordered in the `$source` order, not in the `$resultKeys` order:
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140766
	 * @var array(string => string) $resultWithGarbage
	 */
	$resultWithGarbage = jua_merge_numeric($resultKeys, ju_ita($a));
	return array_intersect_key($resultWithGarbage, $resultKeys);
}