<?php
namespace Justuno\Core\Qa\Message\Failure;
# 2020-06-17 "Port the `Df\Qa\Message\Failure\Exception` class": https://github.com/justuno-com/core/issues/52
final class Exception extends \Justuno\Core\Qa\Message\Failure {
	/**
	 * @override
	 * @see \Justuno\Core\Qa\Message::main()
	 * @used-by \Justuno\Core\Qa\Message::report()
	 * @return string
	 */
	protected function main() {
		$r = $this->e()->messageL(); /** @var string $r */
		return !$this->e()->isMessageHtml() ? $r : strip_tags($r);
	}

	/**
	 * @override
	 * @see \Justuno\Core\Qa\Message\Failure::postface()
	 * @used-by \Justuno\Core\Qa\Message::report()
	 * @return string
	 */
	protected function postface() {return $this->sections($this->sections($this->e()->comments()), parent::postface());}

	/**
	 * 2016-08-20
	 * @override
	 * @see \Justuno\Core\Qa\Message::reportNamePrefix()
	 * @used-by \Justuno\Core\Qa\Message::reportName()
	 * @return string|string[]
	 */
	protected function reportNamePrefix() {return $this[self::P__REPORT_NAME_PREFIX] ?: $this->e()->reportNamePrefix();}

	/**
	 * @override
	 * @see \Justuno\Core\Qa\Message\Failure::stackLevel()
	 * @used-by \Justuno\Core\Qa\Message\Failure::frames()
	 * @return int
	 */
	protected function stackLevel() {return $this->e()->getStackLevelsCountToSkip();}

	/**
	 * @override
	 * @see \Justuno\Core\Qa\Message\Failure::trace()
	 * @used-by \Justuno\Core\Qa\Message\Failure::frames()
	 * @return array(array(string => string|int))
	 */
	protected function trace() {return ju_ef($this->e())->getTrace();}

	/**
	 * @used-by main()
	 * @used-by stackLevel()
	 * @used-by trace()
	 * @return \Justuno\Core\Exception
	 */
	private function e() {return juc($this, function() {return ju_ewrap($this[self::P__EXCEPTION]);});}

	/**
	 * @used-by e()
	 * @used-by ju_log_l()
	 */
	const P__EXCEPTION = 'exception';

	/**
	 * 2020-01-31
	 * @used-by ju_log_l()
	 * @used-by reportNamePrefix()
	 */
	const P__REPORT_NAME_PREFIX = 'reportNamePrefix';

	/**
	 * @used-by ju_log_l()
	 * @param array(string => mixed) $p [optional]
	 * @return self
	 */
	static function i(array $p = []) {return new self($p);}
}