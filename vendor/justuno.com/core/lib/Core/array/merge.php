<?php
use Justuno\Core\Exception as DFE;

/**
 * 2015-02-18
 * 2020-06-26 "Port the `df_extend` function": https://github.com/justuno-com/core/issues/158
 * @used-by ju_extend()
 * @used-by ju_log_l()
 * @used-by ju_sentry()
 * @param array(string => mixed) $defaults
 * @param array(string => mixed) $newValues
 * @return array(string => mixed)
 * @throws DFE
 */
function ju_extend(array $defaults, array $newValues) {/** @var array(string => mixed) $r */
	$r = $defaults;
	foreach ($newValues as $key => $newValue) {
		/** @var int|string $key */ /** @var mixed $newValue */ /** @var mixed $defaultValue */
		$defaultValue = jua($defaults, $key);
		if (!is_array($defaultValue)) {
			if (is_null($newValue)) {
				unset($r[$key]);
			}
			else {
				$r[$key] = $newValue;
			}
		}
		else {
			if (is_array($newValue)) {
				$r[$key] = ju_extend($defaultValue, $newValue);
			}
			else {
				if (is_null($newValue)) {
					unset($r[$key]);
				}
				else {
					ju_error(
						"ju_extend: the default value of key «{$key}» is an array {defaultValue},"
						. "\nbut the programmer mistakenly tries to substitute it"
						. ' with the value {newValue} of type «{newType}».'
						. "\nThe new value should be an array or `null`."
						,[
							'{defaultValue}' => ju_t()->singleLine(ju_dump($defaultValue))
							,'{newType}' => gettype($newValue)
							,'{newValue}' => ju_dump($newValue)
						]
					);
				}
			}
		}
	}
	return $r;
}

/**
 * 2020-06-13 "Port the `dfa_merge_numeric` function": https://github.com/justuno-com/core/issues/14
 * Plain `array_merge($r, $b)` works wronly,
 * if $b contains contains SOME numeric-string keys like "99":
 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
 * https://stackoverflow.com/a/5929671
 * @used-by jua_select_ordered()
 * @param array(string|int => mixed) $r
 * @param array(string|int => mixed) $b
 * @return array(string|int => mixed)
 */
function jua_merge_numeric(array $r, array $b) {
	foreach ($b as $k => $v) {
		$r[$k] = $v;
	}
	return $r;
}
