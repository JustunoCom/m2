<?php
use Justuno\Core\Exception as DFE;
/**
 * 2020-06-14 "Port the `dfa_deep` function": https://github.com/justuno-com/core/issues/18
 * @used-by jua()
 * @used-by \Justuno\Core\Config\Backend::value()
 * @used-by \Justuno\Core\O::offsetExists()
 * @used-by \Justuno\Core\O::offsetGet()
 * @param array(string => mixed) $a
 * @param string|string[]|null $path
 * @param mixed $d [optional]
 * @return mixed|null
 * @throws DFE
 */
function jua_deep(array $a, $path, $d = null) {/** @var mixed|null $r */
	if (ju_nes($path)) {
		$r = $a;
	}
	else if (is_array($path)) {
		$pathParts = $path;
	}
	else {
		ju_param_sne($path, 1);
		if (isset($a[$path])) {
			$r = $a[$path];
		}
		else {
			$pathParts = ju_explode_xpath($path); /** @var string[] $pathParts */
		}
	}
	if (!isset($r)) {
		$r = null;
		/** @noinspection PhpUndefinedVariableInspection */
		while ($pathParts) {
			$r = jua($a, array_shift($pathParts));
			if (is_array($r)) {
				$a = $r;
			}
			else {
				if ($pathParts) {
					$r = null;
				}
				break;
			}
		}
	}
	return is_null($r) ? $d : $r;
}

/**
 * 2015-12-07
 * 2020-08-21 "Port the `dfa_deep_set` function" https://github.com/justuno-com/core/issues/224
 * @used-by \Justuno\Core\O::offsetSet()
 * @param array(string => mixed) $array
 * @param string|string[] $path
 * @param mixed $value
 * @return array(string => mixed)
 * @throws DFE
 */
function jua_deep_set(array &$array, $path, $value) {
	$pathParts = ju_explode_xpath($path); /** @var string[] $pathParts */
	$a = &$array; /** @var array(string => mixed) $a */
	while ($pathParts) {
		$key = array_shift($pathParts); /** @var string $key */
		if (!isset($a[$key])) {
			$a[$key] = [];
		}
		$a = &$a[$key];
		if (!is_array($a)) {
			$a = [];
		}
	}
	$a = $value;
	return $array;
}

/**
 * 2017-07-13
 * 2020-08-21 "Port the `dfa_deep_unset` function" https://github.com/justuno-com/core/issues/225
 * @see jua_unset()
 * @used-by jua_deep_unset()
 * @used-by \Justuno\Core\O::offsetUnset()
 * @param array(string => mixed) $a
 * @param string|string[] $path
 * @throws DFE
 */
function jua_deep_unset(array &$a, $path) {
	if (!is_array($path)) {
		ju_param_sne($path, 1);
		$path = ju_explode_xpath($path);
	}
	/**
	 * 2017-07-13
	 * @uses array_shift не выдаёт предупреждений для пустого массива.
	 * @var string|null $first
	 */
	if ($first = array_shift($path)) {
		if (!$path) {
			unset($a[$first]);
		}
		else {
			jua_deep_unset($a[$first], $path);
		}
	}
}