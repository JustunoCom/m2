<?php
use Exception as E;
use Justuno\Core\Qa\Trace;
use Justuno\Core\Qa\Trace\Formatter;

/**
 * 2020-06-16 "Port the `df_bt` function": https://github.com/justuno-com/core/issues/27
 * @used-by \Justuno\Core\Exception::__construct()
 * @param int $levelsToSkip
 */
function ju_bt($levelsToSkip = 0) {ju_report('bt-{date}-{time}.log', ju_bt_s(++$levelsToSkip));}

/**
 * 2020-06-16 "Port the `df_bt_s` function": https://github.com/justuno-com/core/issues/28
 * @used-by ju_bt()
 * @used-by ju_log_l()
 * @param int|E $p
 * @return string
 */
function ju_bt_s($p = 0) {return Formatter::p(
	new Trace($p instanceof E ? $p->getTrace() : array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), $p))
);}