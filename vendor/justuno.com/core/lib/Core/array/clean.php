<?php
/**
 * 2015-02-07
 * 2020-06-18 "Port the `df_clean` function": https://github.com/justuno-com/core/issues/58
 * @used-by ju_ccc()
 * @used-by ju_kv()
 * @used-by ju_kv_table()
 * @used-by ju_page_result()
 * @used-by \Justuno\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Justuno\Core\Sentry\Client::capture()
 * @used-by \Justuno\Core\Sentry\Client::send()
 * @param mixed[] $r
 * @param mixed ...$k [optional]
 * @return mixed[]
 */
function ju_clean(array $r, ...$k) {/** @var mixed[] $r */return ju_clean_r(
	$r, array_merge([false], ju_args($k)), false
);}

/**
 * 2020-02-05
 * 2020-06-18 "Port the `df_clean_r` function": https://github.com/justuno-com/core/issues/59
 * @see df_clean()
 * 1) It works recursively.
 * 2) I does not remove `false`.
 * @used-by ju_clean()
 * @used-by ju_clean_r()
 * @param mixed[] $r
 * @param mixed[] $k
 * @param bool $req [optional]
 * @return mixed[]
 */
function ju_clean_r(array $r, $k = [], $req = true) {/** @var mixed[] $r */
	/** 2020-02-05 @see array_unique() does not work correctly here, even with the @see SORT_REGULAR flag. */
	$k = array_merge($k, ['', null, []]);
	if ($req) {
		$r = ju_map($r, function($v) use($k) {return !is_array($v) ? $v : ju_clean_r($v, $k);});
	}
	return ju_filter($r, function($v) use($k) {return !in_array($v, $k, true);});
}

/**
 * 2016-11-08
 * 2020-02-05 Now it correcly handles non-associative arrays.
 * 2020-06-18 "Port the `df_filter` function": https://github.com/justuno-com/core/issues/61
 * @used-by ju_clean_r()
 * @param callable|array(int|string => mixed)|array[]\Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|\Traversable $a2
 * @return array(int|string => mixed)
 */
function ju_filter($a1, $a2) { /** @var array $r */
	# 2020-03-02
	# The square bracket syntax for array destructuring assignment (`[…] = […]`) requires PHP ≥ 7.1:
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	list($a, $f) = juaf($a1, $a2); /** @var array|\Traversable $a */ /** @var callable $f */
	$a = ju_ita($a);
	$r = array_filter(ju_ita($a), $f);
	return ju_is_assoc($a) ? $r : array_values($r);
}