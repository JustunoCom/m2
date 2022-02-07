<?php
use Justuno\Core\Exception as DFE;

/**
 * 2020-06-16 "Port the `df_find` function": https://github.com/justuno-com/core/issues/31
 * @used-by ju_ends_with()
 * @used-by ju_starts_with()
 * @param array|callable|\Traversable $a1
 * @param array|callable|\Traversable $a2
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @param int $keyPosition [optional]
 * @return mixed|null
 * @throws DFE
 */
function ju_find($a1, $a2, $pAppend = [], $pPrepend = [], $keyPosition = 0) {
	# 2020-03-02
	# The square bracket syntax for array destructuring assignment (`[…] = […]`) requires PHP ≥ 7.1:
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	list($a, $f) = juaf($a1, $a2); /** @var array|\Traversable $a */ /** @var callable $f */
	$pAppend = ju_array($pAppend); $pPrepend = ju_array($pPrepend);
	$r = null; /** @var mixed|null $r */
	foreach ($a as $k => $v) {/** @var int|string $k */ /** @var mixed $v */ /** @var mixed[] $primaryArgument */
		switch ($keyPosition) {
			case JU_BEFORE:
				$primaryArgument = [$k, $v];
				break;
			case JU_AFTER:
				$primaryArgument = [$v, $k];
				break;
			default:
				$primaryArgument = [$v];
		}
		if ($fr = call_user_func_array($f, array_merge($pPrepend, $primaryArgument, $pAppend))) {
			$r = !is_bool($fr) ? $fr : $v;
			break;
		}
	}
	return $r;
}


