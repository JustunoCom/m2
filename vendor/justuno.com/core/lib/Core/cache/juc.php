<?php
use Justuno\Core\RAM;

/**
 * 2016-08-31
 * 2020-08-14 "Port the `dfc` function" https://github.com/justuno-com/core/issues/194
 * @used-by \Justuno\Core\Config\Backend::value()
 * @used-by \Justuno\Core\Config\Source::pathA()
 * @used-by \Justuno\Core\Exception::standard()
 * @used-by \Justuno\Core\Format\Html\Tag::content()
 * @used-by \Justuno\Core\Format\Html\Tag::shouldAttributesBeMultiline()
 * @used-by \Justuno\Core\Format\Html\Tag::tag()
 * @used-by \Justuno\Core\Qa\Message::report()
 * @used-by \Justuno\Core\Qa\Message\Failure\Exception::e()
 * @used-by \Justuno\Core\Qa\Trace\Frame::context()
 * @used-by \Justuno\Core\Qa\Trace\Frame::functionA()
 * @used-by \Justuno\Core\Qa\Trace\Frame::method()
 * @used-by \Justuno\Core\Qa\Trace\Frame::methodParameter()
 * @param object $o
 * @param \Closure $m
 * @param mixed[] $a [optional]
 * @param bool $unique [optional]
 * @param int $offset [optional]
 * @return mixed
 */
function juc($o, \Closure $m, array $a = [], $unique = true, $offset = 0) {
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2 + $offset)[1 + $offset]; /** @var array(string => string) $b */
	if (!isset($b['class'], $b['function'])) {
		ju_error("[juc] Invalid backtrace frame:\n" . ju_dump($b)); # 2017-01-02 Usually it means that $offset is wrong.
	}
	/** @var string $k */
	$k = "{$b['class']}::{$b['function']}" . (!$a ? null : ju_hash_a($a)) . ($unique ? null : spl_object_hash($m));
	# 2017-01-12 https://3v4l.org/0shto
	return property_exists($o, $k) ? $o->$k : $o->$k = $m(...$a);
}

/**
 * 2020-06-13 "Port the `dfcf` function": https://github.com/justuno-com/core/issues/5
 * @used-by ju_asset_exists()
 * @used-by ju_cli_user()
 * @used-by ju_core_version()
 * @used-by ju_db_version()
 * @used-by ju_domain_current()
 * @used-by ju_magento_version()
 * @used-by ju_module_file()
 * @used-by ju_module_name()
 * @used-by ju_msi()
 * @used-by ju_msi_website2stockId()
 * @used-by ju_my_local()
 * @used-by ju_o()
 * @used-by ju_sentry_m()
 * @used-by ju_table()
 * @used-by \Justuno\Core\Config\Settings::s()
 * @used-by \Justuno\Core\Qa\Trace\Formatter::p()
 * @used-by \Justuno\M2\Store::v()
 * @param \Closure $f
 * @param mixed[] $a [optional]
 * @param string[] $tags [optional]
 * @param bool $unique [optional]
 * @param int $offset [optional]
 * @return mixed
 */
function jucf(\Closure $f, array $a = [], array $tags = [], $unique = true, $offset = 0) {
	$b = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2 + $offset)[1 + $offset]; /** @var array(string => string) $b */
	/** @var string $k */
	$k = (!isset($b['class']) ? null : $b['class'] . '::') . $b['function']
		. (!$a ? null : '--' . ju_hash_a($a))
		. ($unique ? null : '--' . spl_object_hash($f))
	;
	$r = ju_ram(); /** @var RAM $r */
	/**
	 * 2017-01-12
	 * The following code will return `3`:
	 * 		$a = function($a, $b) {return $a + $b;};
	 * 		$b = [1, 2];
	 * 		echo $a(...$b);
	 * https://3v4l.org/0shto
	 */
	return $r->exists($k) ? $r->get($k) : $r->set($k, $f(...$a), $tags);
}