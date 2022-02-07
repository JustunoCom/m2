<?php
/**
 * 2020-06-14 "Port the `df_array` function": https://github.com/justuno-com/core/issues/21
 * @used-by ju_explode_xpath()
 * @used-by ju_fe_init()
 * @used-by ju_find()
 * @used-by ju_map()
 * @param mixed|mixed[] $v
 * @return mixed[]|string[]|float[]|int[]
 */
function ju_array($v) {return is_array($v) ? $v : [$v];}

/**
 * 2020-06-13 "Port the `df_ita` function": https://github.com/justuno-com/core/issues/15
 * @used-by ju_filter()
 * @used-by ju_map()
 * @used-by jua_select_ordered()
 * @used-by juak_transform()
 * @param \Traversable|array $t
 * @return array
 */
function ju_ita($t) {return is_array($t) ? $t : iterator_to_array($t);}

/**
 * 2020-06-14 "Port the `dfa_flatten` function": https://github.com/justuno-com/core/issues/17
 * http://stackoverflow.com/a/1320156
 * @used-by ju_cc()
 * @used-by ju_cc_n()
 * @used-by ju_cc_path()
 * @used-by ju_cc_s()
 * @used-by ju_ccc()
 * @used-by ju_contains()
 * @used-by ju_csv_pretty()
 * @used-by ju_explode_class_camel()
 * @used-by ju_explode_xpath()
 * @used-by ju_mail()
 * @param array $a
 * @return mixed[]
 */
function jua_flatten(array $a) {
	$r = []; /** @var mixed[] $r */
	array_walk_recursive($a, function($a) use(&$r) {$r[]= $a;});
	return $r;
}

/**
 * 2016-09-02
 * @see dfa_deep_unset()
 * @uses array_flip() correctly handles empty arrays.
 * 2019-11-15
 * Previously, it was used as:
 * 		$this->_data = dfa_unset($this->_data, 'can_use_default_value', 'can_use_website_value', 'scope');
 * I replaced it with:
 * 		$this->unsetData(['can_use_default_value', 'can_use_website_value', 'scope']);
 * 2021-03-06 "Port the `dfa_unset` function": https://github.com/justuno-com/core/issues/350
 * @used-by \Justuno\Core\Config\Backend::value()
 * @param array(string => mixed) $a
 * @param string ...$k
 * @return array(string => mixed)
 */
function jua_unset(array $a, ...$k) {return array_diff_key($a, array_flip(ju_args($k)));}

/**
 * 2020-06-16 "Port the `dfaf` function": https://github.com/justuno-com/core/issues/32
 * @used-by ju_filter()
 * @used-by ju_find()
 * @used-by ju_map()
 * @used-by juak_transform()
 * @param array|callable|\Traversable $a
 * @param array|callable|\Traversable $b
 * @return array(int|string => mixed)
 */
function juaf($a, $b) {
	# 2020-02-15
	# «A variable is expected to be a traversable or an array, but actually it is a «object»»:
	# https://github.com/tradefurniturecompany/site/issues/36
	$ca = is_callable($a); /** @var bool $ca */
	$cb = is_callable($b); /** @var bool $ca */
	if (!$ca || !$cb) {
		ju_assert($ca || $cb);
		$r = $ca ? [ju_assert_traversable($b), $a] : [ju_assert_traversable($a), $b];
	}
	else {
		$ta = ju_check_traversable($a); /** @var bool $ta */
		$tb = ju_check_traversable($b); /** @var bool $tb */
		if ($ta && $tb) {
			ju_error('juaf(): both arguments are callable and traversable: %s and %s.', ju_type($a), ju_type($b));
		}
		ju_assert($ta || $tb);
		$r = $ta ? [$a, $b] : [$b, $a];
	}
	return $r;
}

/**
 * 2021-01-28
 * @used-by ju_url_bp()
 * @used-by \Justuno\M2\Store::v()
 * @param int|string $v
 * @param array(int|string => mixed) $map
 * @return int|string|mixed
 */
function jutr($v, array $map) {return jua($map, $v, $v);}