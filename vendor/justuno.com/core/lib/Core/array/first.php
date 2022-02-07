<?php
/**
 * 2020-06-17 "Port the `df_first` function": https://github.com/justuno-com/core/issues/43
 * @used-by ju_caller_c()
 * @used-by ju_class_f()
 * @used-by ju_file_name()
 * @used-by ju_sprintf()
 * @used-by ju_sprintf_strict()
 * @used-by \Justuno\M2\Store::v()
 * @param array $a
 * @return mixed|null
 */
function ju_first(array $a) {return !$a ? null : reset($a);}

/**
 * 2020-08-19 "Port the `df_last` function" https://github.com/justuno-com/core/issues/200
 * @see ju_first()
 * @see ju_tail()
 * @used-by ju_class_l()
 * @used-by ju_package_name_l()
 * @param mixed[] $array
 * @return mixed|null
 */
function ju_last(array $array) {return !$array ? null : end($array);}

/**
 * 2020-06-17 "Port the `df_tail` function": https://github.com/justuno-com/core/issues/39
 * @used-by ju_error_create()
 * @used-by ju_sprintf_strict()
 * @param mixed[] $a
 * @return mixed[]|string[]
 */
function ju_tail(array $a) {return array_slice($a, 1);}