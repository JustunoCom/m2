<?php
use Justuno\Core\Qa\Message\Failure\Exception as QE;
use Exception as E;
use Magento\Framework\DataObject as _DO;

/**
 * 2020-06-22 "Port the `df_log` function": https://github.com/justuno-com/core/issues/117
 * @used-by \Justuno\Core\Config\Backend::save()
 * @used-by \Justuno\Core\Qa\Message::log()
 * @param _DO|mixed[]|mixed|E $v
 * @param string|object|null $m [optional]
 */
function ju_log($v, $m = null) {ju_log_l($m, $v); ju_sentry($m, $v);}

/**
 * 2017-01-11
 * 2020-06-17 "Port the `df_log_e` function": https://github.com/justuno-com/core/issues/50
 * @used-by ju_error()
 * @used-by \Justuno\Core\Qa\Trace\Formatter::frame()
 * @param E $e
 * @param string|object|null $m [optional]
 * @param string|mixed[] $d [optional]
 * @param string|bool|null $suf [optional]
 */
function ju_log_e($e, $m = null, $d = [], $suf = null) {ju_log_l($m, $e, $d, !is_null($suf) ? $suf : ju_caller_f());}

/**
 * 2017-01-11
 * 2020-06-17 "Port the `df_log_l` function": https://github.com/justuno-com/core/issues/51
 * @used-by ju_caller_m()
 * @used-by ju_log()
 * @used-by ju_log_e()
 * @used-by \Justuno\Core\Qa\Trace\Formatter::frame()
 * @param string|object|null $m
 * @param string|mixed[]|E $p2
 * @param string|mixed[]|E $p3 [optional]
 * @param string|bool|null $suf [optional]
 */
function ju_log_l($m, $p2, $p3 = [], $suf = null) {
	/** @var E|null $e */ /** @var array|string|mixed $d */ /** @var string|null $suf */
	list($e, $d, $suf) = $p2 instanceof E ? [$p2, $p3, $suf] : [null, $p2, $p3];
	$suf = $suf ?: ju_caller_f();
	if (is_array($d)) {
		$d = ju_extend($d, ['Mage2.PRO' =>
			['mage2pro/core' => ju_core_version(), 'Magento' => ju_magento_version(), 'PHP' => phpversion()]
			+ (ju_is_cli()
				? ['Command' => ju_cli_cmd()]
				: (
					['Referer' => ju_referer(), 'URL' => ju_current_url()]
					+ (!ju_request_o()->isPost() ? [] : ['Post' => $_POST])
				)
			)
		]);
	}
	$d = !$d ? null : (is_string($d) ? $d : ju_json_encode($d));
	ju_report(
		ju_ccc('--', 'mage2.pro/' . ju_ccc('-', ju_report_prefix($m), '{date}--{time}'), $suf) .  '.log'
		,ju_cc_n(
			$d
			,!$e ? null : ['EXCEPTION', QE::i([
				QE::P__EXCEPTION => $e, QE::P__REPORT_NAME_PREFIX => ju_report_prefix($m), QE::P__SHOW_CODE_CONTEXT => false
			])->report(), "\n\n"]
			,ju_bt_s(1)
		)
	);
}

/**
 * 2017-04-03
 * 2018-07-06 The `$append` parameter has been added.
 * 2020-02-14 If $append is `true`, then $m will be written on a new line.
 * 2020-06-20 "Port the `df_report` function": https://github.com/justuno-com/core/issues/93
 * @used-by ju_bt()
 * @used-by ju_log_l()
 * @used-by \Justuno\Core\Qa\Message::log()
 * @param string $f
 * @param string $m
 * @param bool $append [optional]
 */
function ju_report($f, $m, $append = false) {
	if ('' !== $m) {
		ju_param_s($m, 1);
		$f = ju_file_ext_def($f, 'log');
		$p = BP . '/var/log'; /** @var string $p */
		ju_file_write($append ? "$p/$f" : ju_file_name($p, $f), $m, $append);
	}
}

/**
 * 2020-01-31
 * 2020-08-21 "Port the `df_report_prefix` function" https://github.com/justuno-com/core/issues/214
 * @used-by ju_log_l()
 * @param string|object|null $m [optional]
 * @return string|null
 */
function ju_report_prefix($m = null) {return !$m ? null : (ju_package_name_l($m) ?: ju_report_prefix($m, '-'));}