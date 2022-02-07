<?php
use Closure as F;

/**
 * 2015-12-30
 * 2017-07-09 Now the function can accept an array as $object.
 * 2020-06-18 "Port the `df_call` function": https://github.com/justuno-com/core/issues/72
 * @used-by ju_each()
 * @param object|mixed|array $o
 * @param string|callable|F $m
 * @param mixed[] $p [optional]
 * @return mixed
 */
function ju_call($o, $m, $p = []) {/** @var mixed $r */
	if (is_array($o) && ju_is_assoc($o)) {
		$r = jua($o, $m);
	}
	elseif (!is_string($m)) {
		$r = call_user_func_array($m, array_merge([$o], $p));
	}
	else {
		$functionExists = function_exists($m); /** @var bool $functionExists */
		/**
		 * 2020-02-05
		 * 1) @uses is_callable() always returns `true` for an object which the magic `__call` method
		 * (e.g., for all @see \Magento\Framework\DataObject ancestors),
		 * but it returns `false` for private and protected (so non-callable) methods.
		 * 2) @uses method_exists() returns `true` even for private and protected (so non-callable) method,
		 * but it returns `false` for absent methods handled by `__call`.
		 * 3) The conjunction of these 2 checks returns `true` only for publicly accessible and really exists
		 * (not handled by `__call`) methods.
		 */
		$methodExists = is_callable([$o, $m]) && method_exists($o, $m); /** @var bool $methodExists */
		$callable = null; /** @var mixed $callable */
		if ($functionExists && !$methodExists) {
			$callable = $m;
			$p = array_merge([$o], $p);
		}
		elseif ($methodExists && !$functionExists) {
			$callable = [$o, $m];
		}
		if ($callable) {
			$r = call_user_func_array($callable, $p);
		}
		else if (ju_has_gd($o)) {
			$r = juad($o, $m);
		}
		elseif (!$functionExists) {
			ju_error("Unable to call «{$m}».");
		}
		else {
			ju_error("An ambiguous name: «{$m}».");
		}
	}
	return $r;
}

/**
 * 2016-01-14
 * 2019-06-05 Parent functions with multiple different arguments are not supported!
 * 2020-06-18 "Port the `df_call_a` function": https://github.com/justuno-com/core/issues/79
 * @used-by df_tab()
 * @used-by ju_explode_camel()
 * @used-by ju_lcfirst()
 * @used-by ju_link_inline()
 * @used-by ju_ucfirst()
 * @used-by ju_xml_output_plain()
 * @param callable $f
 * @param mixed[]|mixed[][] $parentArgs
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @param int $keyPosition [optional]
 * @return mixed|mixed[]
 */
function ju_call_a(callable $f, array $parentArgs, $pAppend = [], $pPrepend = [], $keyPosition = 0) {
	/**
	 * 2016-11-13 We can not use @see ju_args() here
	 * 2019-06-05
	 * The parent function could be called in 3 ways:
	 * 1) With a single array argument.
	 * 2) With a single scalar (non-array) argument.
	 * 3) With multiple arguments.
	 * `1 === count($parentArgs)` in the 1st and 2nd cases.
	 *  1 <> count($parentArgs) in the 3rd case.
	 * We should return an array in the 1st and 3rd cases, and a scalar result in the 2nd case.
	 */
	if (1 === count($parentArgs)) {
		# 2019-06-05 It is the 1st or the 2nd case: a single argument (a scalar or an array).
		$parentArgs = $parentArgs[0];
	}
	return
		# 2019-06-05 It is the 2nd case: a single scalar (non-array) argument
		!is_array($parentArgs)
		? call_user_func_array($f, array_merge($pPrepend, [$parentArgs], $pAppend))
		: ju_map($f, $parentArgs, $pAppend, $pPrepend, $keyPosition
	);
}

/**
 * 2020-06-13 "Port the `df_call_if` function": https://github.com/justuno-com/core/issues/11
 * https://3v4l.org/iUQGl
 *	 function a($b) {return is_callable($b);}
 *	 a(function() {return 0;}); возвращает true
 * https://3v4l.org/MfmCj
 *	is_callable('intval') возвращает true
 * @used-by ju_if()
 * @used-by ju_if1()
 * @used-by jua()
 * @param mixed|callable $v
 * @param mixed ...$a [optional]
 * @return mixed
 */
function ju_call_if($v, ...$a) {return is_callable($v) && !is_string($v) && !is_array($v) ? call_user_func_array($v, $a) : $v;}