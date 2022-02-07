<?php
/**
 * 2020-02-15
 * ju_cli_argv(1) returns «cron:run» even in the `bin/magento cron:run --bootstrap=standaloneProcessStarted=1` case.
 * 2020-06-17 "Port the `df_is_cron` function": https://github.com/justuno-com/core/issues/45
 * @used-by ju_error()
 * @return bool
 */
function ju_is_cron() {return ju_is_bin_magento() && 'cron:run' === ju_cli_argv(1);}