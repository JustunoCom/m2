<?php
use Justuno\Core\Exception as DFE;
use Exception as E;
use Magento\Framework\Phrase as P;
/**
 * 2016-07-18
 * 2020-08-21 "Port the `df_ef` function" https://github.com/justuno-com/core/issues/208
 * @used-by \Justuno\Core\Qa\Message\Failure\Exception::trace()
 * @param E $e
 * @return E
 */
function ju_ef(E $e) {while ($e->getPrevious()) {$e = $e->getPrevious();} return $e;}

/**
 * 2020-06-15 "Port the `df_ets` function": https://github.com/justuno-com/core/issues/24
 * @used-by ju_message_error()
 * @used-by ju_sprintf_strict()
 * @used-by \Justuno\Core\Exception::__construct()
 * @used-by \Justuno\Core\Qa\Message::log()
 * @used-by \Justuno\Core\Qa\Message\Failure\Exception::e()
 * @used-by \Justuno\Core\Qa\Trace\Formatter::frame()
 * @used-by \Justuno\Core\Zf\Validate\IntT::filter()
 * @param E|P|string $e
 * @return P|string
 */
function ju_ets($e) {return ju_adjust_paths_in_message(
	!$e instanceof E ? $e : ($e instanceof DFE ? $e->message() : $e->getMessage())
);}

/**
 * 2016-07-31
 * 2020-06-17 "Port the `df_ewrap` function": https://github.com/justuno-com/core/issues/38
 * @used-by ju_error_create()
 * @param E $e
 * @return DFE
 */
function ju_ewrap($e) {return DFE::wrap($e);}