<?php
/**
 * 2017-11-19
 * 2021-02-24
 * @used-by \Justuno\M2\Response::p()
 * @param int $o [optional]
 * @return string
 */
function ju_caller_c($o = 0) {return ju_first(ju_explode_method(ju_caller_m(++$o)));}

/**
 * 2017-03-28 If the function is called from a closure, then it will go up through the stask until it leaves all closures.
 * 2020-08-19 "Port the `df_caller_entry` function" https://github.com/justuno-com/core/issues/207
 * @used-by ju_caller_f()
 * @used-by ju_caller_m()
 * @param int $o [optional]
 * @return array(string => string|int)
 */
function ju_caller_entry($o = 0) {
	/** @var array(int => array(string => mixed)) $bt */
	/**
	 * 2018-04-24
	 * I do not understand why did I use `2 + $offset` here before.
	 * Maybe the @uses array_slice() was included in the backtrace in previous PHP versions (e.g. PHP 5.6)?
	 * array_slice() is not included in the backtrace in PHP 7.1.14 and in PHP 7.0.27
	 * (I have checked it in the both XDebug enabled and disabled cases).
	 * 2019-01-14
	 * It seems that we need `2 + $offset` because the stack contains:
	 * 1) the current function: df_caller_entry
	 * 2) the function who calls df_caller_entry: df_caller_ff or df_caller_mm
	 * 3) the function who calls df_caller_ff or df_caller_mm: it should be the result.
	 * So the offset is 2.
	 * The previous code failed the @see \Df\API\Facade::p() method in the inkifi.com store.
	 */
	$bt = array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 2 + $o);
	while ($r = array_shift($bt) /** @var array(string => string|int) $r */) {
		$f = $r['function']; /** @var string $f */
		if (!ju_contains($f, '{closure}') && !in_array($f, ['juc', 'jucf'])) {
			break;
		}
	}
	return $r;
}

/**
 * 2016-08-10
 * The original (not used now) implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L109-L111
 * 2017-01-12
 * The df_caller_ff() implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L113-L123
 * 2020-07-08 The function's new implementation is from the previous df_caller_ff() function.
 * 2020-08-19 "Port the `df_caller_f` function" https://github.com/justuno-com/core/issues/206
 * @used-by ju_log_e()
 * @used-by ju_log_l()
 * @used-by ju_oqi_amount()
 * @used-by ju_prop()
 * @used-by \Justuno\Core\Config\Settings::b()
 * @used-by \Justuno\Core\Config\Settings::v()
 * @param int $o [optional]
 * @return string
 */
function ju_caller_f($o = 0) {return ju_caller_entry(++$o)['function'];}

/**
 * 2016-08-10
 * The original (not used now) implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L125-L136
 * 2017-03-28
 * The df_caller_mm() implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L155-L169
 * 2020-07-08 The function's new implementation is from the previous df_caller_mm() function.
 * 2020-08-19 "Port the `df_caller_m` function" https://github.com/justuno-com/core/issues/205
 * @used-by ju_caller_c()
 * @used-by ju_prop()
 * @param int $o [optional]
 * @return string
 */
function ju_caller_m($o = 0) {
	$bt = ju_caller_entry(++$o); /** @var array(string => int) $bt */
	$class = jua($bt, 'class'); /** @var string $class */
	if (!$class) {
		ju_log_l(null, $m = "ju_caller_m(): no class.\nbt is:\n$bt", __FUNCTION__); /** @var string $m */
		ju_error($m);
	}
	return "$class::{$bt['function']}";
}