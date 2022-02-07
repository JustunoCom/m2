<?php
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}

/**
 * 2016-10-14
 * 2021-03-07 "Port the `df_trim_ds` function": https://github.com/justuno-com/core/issues/370
 * @used-by ju_url_bp()
 * @param string $p
 * @return string
 */
function ju_trim_ds($p) {return ju_trim($p, '/\\');}

/**
 * 2015-11-30
 * 2020-08-13 "Port the `df_trim_ds_left` function" https://github.com/justuno-com/core/issues/175
 * @used-by ju_path_relative()
 * @param string $p
 * @return string
 */
function ju_trim_ds_left($p) {return ju_trim_left($p, '/\\');}