<?php
/**
 * http://stackoverflow.com/a/15011528
 * http://www.php.net/manual/en/function.filter-var.php
 * filter_var('/C/A/CA559AWLE574_1.jpg', FILTER_VALIDATE_URL) returns `false`.
 * 2021-03-07 "Port the `df_check_url` function": https://github.com/justuno-com/core/issues/369
 * @used-by ju_url_bp()
 * @param $s $string
 * @return bool
 */
function ju_check_url($s) {return false !== filter_var($s, FILTER_VALIDATE_URL);}

/**
 * 2017-10-16
 * 2020-08-22 "Port the `df_check_url_absolute` function" https://github.com/justuno-com/core/issues/251
 * @used-by ju_asset_create()
 * @used-by ju_js()
 * @param string $u
 * @return bool
 */
function ju_check_url_absolute($u) {return ju_starts_with($u, ['http', '//']);}