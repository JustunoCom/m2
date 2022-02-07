<?php
namespace Justuno\Core\Qa\Message;
use Justuno\Core\Qa\Trace;
use Justuno\Core\Qa\Trace\Formatter;
/**
 * 2020-06-17 "Port the `Df\Qa\Message\Failure` class": https://github.com/justuno-com/core/issues/53
 * @see \Justuno\Core\Qa\Message\Failure\Error
 * @see \Justuno\Core\Qa\Message\Failure\Exception
 */
abstract class Failure extends \Justuno\Core\Qa\Message {
	/**
	 * @abstract
	 * @used-by postface()
	 * @see \Justuno\Core\Qa\Message\Failure\Error::trace()
	 * @see \Justuno\Core\Qa\Message\Failure\Exception::trace()
	 * @return array(array(string => string|int))
	 */
	abstract protected function trace();

	/**
	 * @override
	 * @see \Justuno\Core\Qa\Message::postface()
	 * @used-by \Justuno\Core\Qa\Message::report()
	 * @used-by \Justuno\Core\Qa\Message\Failure\Exception::postface()
	 * @see \Justuno\Core\Qa\Message\Failure\Exception::postface()
	 * @return string
	 */
	protected function postface() {return Formatter::p(
		new Trace(array_slice($this->trace(), $this->stackLevel())), $this->a(self::P__SHOW_CODE_CONTEXT, true)
	);}

	/**
	 * @used-by postface()
	 * @see \Justuno\Core\Qa\Message\Failure\Exception::stackLevel()
	 * @see \Justuno\Core\Qa\Message\Failure\Error::stackLevel()
	 * @return int
	 */
	protected function stackLevel() {return 0;}

	/**
	 * @used-by ju_log_l()
	 * @used-by postface()
	 */
	const P__SHOW_CODE_CONTEXT = 'show_code_context';
}