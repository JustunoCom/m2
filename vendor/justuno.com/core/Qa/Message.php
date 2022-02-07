<?php
namespace Justuno\Core\Qa;
/**
 * 2020-06-17 "Port the `Df\Qa\Message` class": https://github.com/justuno-com/core/issues/54
 * @see \Justuno\Core\Qa\Message\Failure
 */
abstract class Message extends \Justuno\Core\O {
	/**
	 * @used-by report()
	 * @see \Justuno\Core\Qa\Message\Failure\Error::main()
	 * @see \Justuno\Core\Qa\Message\Failure\Exception::main()
	 * @see \Justuno\Core\Qa\Message\Notification::main()
	 * @return string
	 */
	abstract protected function main();

	/**
	 * @used-by \Justuno\Core\Qa\Message\Failure\Error::check()
	 * @throws \Exception
	 */
	public final function log() {
		static $inProcess;
		if (!$inProcess) {
			$inProcess = true;
			try {
				ju_report($this->reportName(), $this->report());
				$inProcess = false;
			}
			catch (\Exception $e) {
				ju_log(ju_ets($e));
				throw $e;
			}
		}
	}

	/**
	 * @used-by log()
	 * @used-by mail()
	 * @used-by ju_log_l()
	 * @return string
	 */
	final function report() {return juc($this, function() {return $this->sections(
		Context::render(), $this->preface(), $this->main(), $this->postface()
	);});}

	/**
	 * @used-by report()
	 * @see \Justuno\Core\Qa\Message\Failure::postface()
	 * @return string
	 */
	protected function postface() {return '';}

	/**
	 * @used-by report()
	 * @return string
	 */
	protected function preface() {return '';}

	/**
	 * 2016-08-20
	 * @used-by \Justuno\Core\Qa\Message::log()
	 * @return string
	 */
	protected function reportName() {return 'mage2.pro/' . ju_ccc('-', $this->reportNamePrefix(), '{date}--{time}.log');}

	/**
	 * 2016-08-20
	 * @used-by reportName()
	 * @see \Justuno\Core\Qa\Message\Failure\Exception::reportNamePrefix()
	 * @return string|string[]
	 */
	protected function reportNamePrefix() {return [];}

	/**
	 * @used-by report()
	 * @used-by \Justuno\Core\Qa\Message\Failure\Exception::postface()
	 * @param string|string[] $items
	 * @return string
	 */
	protected function sections($items) {
		if (!is_array($items)) {
			$items = func_get_args();
		}
		/** @var string $s */
		static $s; if (!$s) {$s = "\n" . str_repeat('*', 36) . "\n";};
		return implode($s, array_filter(ju_trim(ju_xml_output_plain($items))));
	}
}