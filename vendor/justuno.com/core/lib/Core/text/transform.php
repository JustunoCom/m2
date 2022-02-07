<?php
/**
 * 2016-01-14
 * 2020-08-21 "Port the `df_lcfirst` function" https://github.com/justuno-com/core/issues/219
 * @see ju_ucfirst()
 * @used-by ju_explode_class_lc()
 * @used-by ju_explode_class_lc_camel()
 * @param string ...$args
 * @return string|string[]
 */
function ju_lcfirst(...$args) {return ju_call_a(function($s) {return
	mb_strtolower(mb_substr($s, 0, 1)) . mb_substr($s, 1)
;}, $args);}

/**
 * 2020-06-18 "Port the `df_ucfirst` function": https://github.com/justuno-com/core/issues/78
 * @see ju_lcfirst()
 * @used-by ju_assert_gd()
 * @param string ...$args
 * @return string|string[]
 */
function ju_ucfirst(...$args) {return ju_call_a(function($s) {return
	mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1)
;}, $args);}