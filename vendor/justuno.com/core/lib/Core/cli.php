<?php
/**
 * 2017-03-15
 * 2020-06-17 "Port the `df_cli_argv` function": https://github.com/justuno-com/core/issues/49
 * @used-by ju_cli_cmd()
 * @used-by ju_cli_script()
 * @used-by ju_is_cron()
 * @param int|null $i [optional]
 * @return string|string[]
 */
function ju_cli_argv($i = null) {return jua(jua($_SERVER, 'argv', []), $i);}

/**
 * 2020-05-24
 * 2020-08-21 "Port the `df_cli_cmd` function" https://github.com/justuno-com/core/issues/209
 * @used-by ju_log_l()
 * @return string
 *
 */
function ju_cli_cmd() {return ju_cc_s(ju_cli_argv());}

/**
 * 2020-02-15
 * 2020-06-17 "Port the `df_cli_script` function": https://github.com/justuno-com/core/issues/48
 * @used-by ju_is_bin_magento()
 * @return string
 */
function ju_cli_script() {return ju_cli_argv(0);}

/**
 * 2016-12-23 http://stackoverflow.com/a/7771601
 * 2020-08-13 "Port the `df_cli_user` function" https://github.com/justuno-com/core/issues/178
 * @see \Magento\Framework\Shell::execute()
 * @used-by ju_sentry_m()
 * @return string
 */
function ju_cli_user() {return jucf(function() {return exec('whoami');});}

/**
 * 2020-02-15
 * 1) `bin/magento` can be called with a path prefix, so I use @uses ju_ends_with()
 * 2) df_cli_script() returns «bin/magento» even in the `php bin/magento ...` case.
 * 2020-06-17 "Port the `df_is_bin_magento` function": https://github.com/justuno-com/core/issues/46
 * @used-by ju_is_cron()
 * @return bool
 */
function ju_is_bin_magento() {return ju_ends_with(ju_cli_script(), 'bin/magento');}

/**
 * 2016-10-25 http://stackoverflow.com/a/1042533
 * 2020-06-17 "Port the `df_is_cli` function": https://github.com/justuno-com/core/issues/36
 * @used-by ju_action_name()
 * @used-by ju_header_utf()
 * @used-by ju_log_l()
 * @used-by ju_sentry_m()
 * @used-by \Justuno\Core\Sentry\Client::__construct()
 * @used-by \Justuno\Core\Sentry\Client::capture()
 * @return bool
 */
function ju_is_cli() {return 'cli' === php_sapi_name();}