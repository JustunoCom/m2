<?php
/**
 * 2019-09-08
 * 2020-08-19 "Port the `df_prop` function" https://github.com/justuno-com/core/issues/204
 * @used-by ju_prop()
 * @used-by \Justuno\Core\Qa\Trace\Frame::showContext()
 */
const JU_N = 'df-null';

/**
 * 2019-04-05
 * 2019-09-08 Now it supports static properties.
 * 2020-08-19 "Port the `df_prop` function" https://github.com/justuno-com/core/issues/204
 * @used-by \Justuno\Core\Qa\Trace\Frame::showContext()
 * @param object|null|\ArrayAccess $o
 * @param mixed|string $v
 * @param string|mixed|null $d [optional]
 * @param string|null $type [optional]
 * @return mixed|object|\ArrayAccess|null
 */
function ju_prop($o, $v, $d = null, $type = null) {/** @var object|mixed|null $r */
	/**
	 * 2019-09-08
	 * 1) My 1st solution was comparing $v with `null`,
	 * but it is wrong because it fails for a code like `$object->property(null)`.
	 * 2) My 2nd solution was using @see func_num_args():
	 * «How to tell if optional parameter in PHP method/function was set or not?»
	 * https://stackoverflow.com/a/3471863
	 * It is wrong because the $v argument is alwaus passed to ju_prop()
	 */
	$isGet = JU_N === $v; /** @vae bool $isGet */
	if ('int' === $d) {
		$type = $d; $d = null;
	}
	/** @var string $k */
	if (is_null($o)) { # 2019-09-08 A static call.
		$k = ju_caller_m();
		static $s; /** @var array(string => mixed) $s */
		if ($isGet) {
			$r = jua($s, $k, $d);
		}
		else {
			$s[$k] = $v;
			$r = null;
		}
	}
	else {
		$k = ju_caller_f();
		if ($o instanceof \ArrayAccess) {
			if ($isGet) {
				$r = !$o->offsetExists($k) ? $d : $o->offsetGet($k);
			}
			else {
				$o->offsetSet($k, $v);
				$r = $o;
			}
		}
		else {
			$a = '_' . __FUNCTION__; /** @var string $a */
			if (!isset($o->$a)) {
				$o->$a = [];
			}
			if ($isGet) {
				$r = jua($o->$a, $k, $d);
			}
			else {
				$prop = &$o->$a;
				$prop[$k] = $v;
				$r = $o;
			}
		}
	}
	return $isGet && 'int' === $type ? intval($r) : $r;
}