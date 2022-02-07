<?php
/**
 * 2017-02-01
 * 2020-01-29
 * 2020-02-04
 * It does not change keys of a non-associative array,
 * but it is applied recursively to nested arrays, so it could change keys their keys.
 * 2020-08-13 "Port the `dfak_transform` function" https://github.com/justuno-com/core/issues/166
 * @used-by juak_transform()
 * @used-by \Justuno\Core\Sentry\Client::tags()
 * @used-by \Justuno\Core\Sentry\Extra::adjust()
 * @param array|callable|\Traversable $a1
 * @param array|callable|\Traversable $a2
 * @param bool $req [optional]
 * @return array(string => mixed)
 */
function juak_transform($a1, $a2, $req = false) {
	# 2020-03-02
	# The square bracket syntax for array destructuring assignment (`[…] = […]`) requires PHP ≥ 7.1:
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	list($a, $f) = juaf($a1, $a2); /** @var array|\Traversable $a */ /** @var callable $f */
	$a = ju_ita($a);
	$as = ju_is_assoc($a); /** @var bool $as */
	return ju_map_kr($a, function($k, $v) use($f, $req, $as) {return [
		!$as ? $k : $f($k), !$req || !is_array($v) ? $v : juak_transform($v, $f, $req)
	];});
}