<?php
use Justuno\Core\Exception as DFE;
use Exception as E;
use Magento\Framework\Phrase;

/**
 * 2020-06-17 "Port the `df_error` function": https://github.com/justuno-com/core/issues/34
 * @used-by ju_assert()
 * @used-by ju_assert_gd()
 * @used-by ju_assert_ge()
 * @used-by ju_assert_gt0()
 * @used-by ju_assert_lt()
 * @used-by ju_assert_ne()
 * @used-by ju_assert_nef()
 * @used-by ju_assert_traversable()
 * @used-by ju_bool()
 * @used-by ju_call()
 * @used-by ju_caller_m()
 * @used-by ju_customer()
 * @used-by ju_date_from_db()
 * @used-by ju_extend()
 * @used-by ju_file_name()
 * @used-by ju_int()
 * @used-by ju_json_decode()
 * @used-by ju_module_file()
 * @used-by ju_oqi_is_leaf()
 * @used-by ju_oqi_qty()
 * @used-by ju_oqi_qty()
 * @used-by ju_order_last()
 * @used-by ju_pad()
 * @used-by ju_product_current()
 * @used-by ju_sentry_m()
 * @used-by ju_sprintf_strict()
 * @used-by ju_string()
 * @used-by ju_try()
 * @used-by juaf()
 * @used-by juc()
 * @used-by \Justuno\Core\Helper\Text::quote()
 * @used-by \Justuno\Core\Qa\Method::throwException()
 * @used-by \Justuno\Core\Qa\Trace\Frame::methodParameter()
 * @used-by \Justuno\Core\Zf\Filter\StringTrim::_splitUtf8()
 * @used-by \Justuno\Core\Zf\Validate\IntT::filter()
 * @used-by \Justuno\M2\Catalog\Diagnostic::p()
 * @used-by \Justuno\M2\Store::v()
 * @param string ...$args
 * @throws DFE
 */
function ju_error(...$args) {
	ju_header_utf();
	$e = ju_error_create(...$args); /** @var DFE $e */
	/**
	 * 2020-02-15
	 * 1) "The Cron log (`magento.cron.log`) should contain a backtrace for every exception logged":
	 * https://github.com/tradefurniturecompany/site/issues/34
	 * 2) The @see \Exception 's backtrace is set when the exception is created, not when it is thrown:
	 * https://3v4l.org/qhd7m
	 * So we have a correct backtrace even without throwing the exception.
	 * 2020-02-17 @see \Df\Cron\Plugin\Console\Command\CronCommand::aroundRun()
	 */
	if (ju_is_cron()) {
		ju_log_e($e);
	}
	throw $e;
}

/**
 * 2016-07-31
 * 2020-06-17 "Port the `df_error_create` function": https://github.com/justuno-com/core/issues/37
 * @used-by ju_error()
 * @param string|string[]|mixed|E|Phrase|null $m [optional]
 * @return DFE
 */
function ju_error_create($m = null) {return
	$m instanceof E ? ju_ewrap($m) :
		new DFE($m instanceof Phrase ? $m : (
			/**
			 * 2019-12-16
			 * I have changed `!$m` to `is_null($m)`.
			 * It passes an empty string ('') directly to @uses \Justuno\Core\Exception::__construct()
			 * and it prevents @uses \Justuno\Core\Exception::__construct() from calling @see df_bt()
			 * @see \Justuno\Core\Exception::__construct():
			 *		if (is_null($m)) {
			 *			$m = __($prev ? df_ets($prev) : 'No message');
			 *			# 2017-02-20 To facilite the «No message» diagnostics.
			 *			if (!$prev) {
			 *				df_bt();
			 *			}
			 *		}
			 */
			is_null($m) ? null : (is_array($m) ? implode("\n\n", $m) : (
				ju_contains($m, '%1') ? __($m, ...ju_tail(func_get_args())) :
					ju_format(func_get_args())
			))
		))
;}