<?php
namespace Justuno\Core;
use \Exception as E;
use Magento\Framework\Exception\LocalizedException as LE;
use Magento\Framework\Phrase;
/**
 * 2020-06-15 "Port the `Df\Core\Exception` class": https://github.com/justuno-com/core/issues/23
 * @used-by ju_param_sne()
 */
class Exception extends LE implements \ArrayAccess {
	/**
	 * @used-by ju_error_create()
	 * @param mixed ...$args
	 */
	function __construct(...$args) {
		$arg0 = jua($args, 0); /** @var string|Phrase|E|array(string => mixed)|null $arg0 */
		$prev = null; /** @var E|LE|null $prev */
		$m = null;  /** @var Phrase|null $m */
		# 2015-10-10
		if (is_array($arg0)) {
			$this->_data = $arg0;
		}
		elseif ($arg0 instanceof Phrase) {
			$m = $arg0;
		}
		elseif (is_string($arg0)) {
			$m = __($arg0);
		}
		elseif ($arg0 instanceof E) {
			$prev = $arg0;
		}
		$arg1 = jua($args, 1); /** @var int|string|E|Phrase|null $arg1 */
		if (!is_null($arg1)) {
			if ($arg1 instanceof E) {
				$prev = $arg1;
			}
			elseif (is_int($prev)) {
				$this->_stackLevelsCountToSkip = $arg1;
			}
			elseif (is_string($arg1) || $arg1 instanceof Phrase) {
				$this->comment((string)$arg1);
			}
		}
		if (is_null($m)) {
			$m = __($prev ? ju_ets($prev) : 'No message');
			# 2017-02-20 To facilite the «No message» diagnostics.
			if (!$prev) {
				ju_bt();
			}
		}
		parent::__construct($m, $prev);
	}

	/**
	 * @used-by __construct()
	 * @param mixed ...$args
	 */
	function comment(...$args) {$this->_comments[]= ju_format($args);}

	/**
	 * @param mixed ...$args
	 */
	function commentPrepend(...$args) {array_unshift($this->_comments, ju_format($args));}

	/**
	 * @used-by \Justuno\Core\Qa\Message_Failure_Exception::preface()
	 * @return string[]
	 */
	function comments() {return $this->_comments;}

	/**
	 * @used-by \Justuno\Core\Qa\Message_Failure_Exception::stackLevel()
	 * @return int
	 */
	function getStackLevelsCountToSkip() {return $this->_stackLevelsCountToSkip;}

	/**
	 * 2016-07-31
	 * @used-by \Justuno\Core\Qa\Message\Failure\Exception::main()
	 * @return bool
	 */
	function isMessageHtml() {return $this->_messageIsHtml;}

	/**
	 * 2016-07-31
	 * @return $this
	 */
	function markMessageAsHtml() {$this->_messageIsHtml = true; return $this;}

	/**
	 * @return string
	 */
	function message() {return $this->getMessage();}

	/**
	 * A message for a buyer.
	 * 2016-10-24
	 * Раньше этот метод возвращал $this->message().
	 * Теперь я думаю, что null логичнее:
	 * низкоуровневые сообщения покупателям показывать всегда неправильно,
	 * а потомки этого класса могут переопределить у себя этот метод
	 * (так, в частности, поступают потмки в платёжных модулях).
	 * @return string|null
	 */
	function messageC() {return null;}

	/**
	 * @used-by messageL()
	 * @used-by messageSentry()
	 * @return string
	 */
	function messageD() {return $this->message();}

	/**
	 * 2016-08-19 Сообщение для журнала.
	 * @used-by \Justuno\Core\Qa\Message\Failure\Exception::main()
	 * @return string
	 */
	function messageL() {return $this->messageD();}

	/**
	 * 2017-01-09
	 * @used-by \Justuno\Core\Sentry\Client::captureException()
	 * @return string
	 */
	function messageSentry() {return $this->messageD();}

	/**
	 * @return bool
	 */
	function needNotifyAdmin() {return true;}

	/**
	 * @return bool
	 */
	function needNotifyDeveloper() {return true;}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @param string $offset
	 * @return bool
	 */
	function offsetExists($offset) {return isset($this->_data[$offset]);}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $offset
	 * @return mixed
	 */
	function offsetGet($offset) {return jua($this->_data, $offset);}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $offset
	 * @param mixed $value
	 */
	function offsetSet($offset, $value) {$this->_data[$offset] = $value;}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $offset
	 */
	function offsetUnset($offset) {unset($this->_data[$offset]);}

	/**
	 * 2016-10-24
	 * @used-by \Justuno\Core\Qa\Message\Failure\Exception::reportNamePrefix()
	 * @return string|string[]
	 */
	final function reportNamePrefix() {return [ju_module_name_lc($this->module()), 'exception'];}

	/**
	 * 2017-01-09
	 * @used-by ju_sentry()
	 * @return array(string => mixed)
	 */
	function sentryContext() {return [];}

	/**
	 * 2017-10-03
	 * @used-by \Justuno\Core\Sentry\Client::captureException()
	 * @return string
	 */
	function sentryType() {return get_class($this);}

	/**
	 * 2015-11-27
	 * Мы не можем перекрыть метод @see \Exception::getMessage(), потому что он финальный.
	 * С другой стороны, наш метод @see \Justuno\Core\Exception::message()
	 * не будет понят стандартной средой,
	 * и мы в стандартной среде не будем иметь диагностического сообщения вовсе.
	 * Поэтому если мы сами не в состоянии обработать исключительную ситуацию,
	 * то вызываем метод @see \Justuno\Core\Exception::standard().
	 * Этот метод конвертирует исключительную ситуацию в стандартную,
	 * и стандартная среда её успешно обработает.
	 * @return \Exception
	 */
	function standard() {return juc($this, function() {return new \Exception($this->message(), 0, $this);});}

	/**
	 * 2017-10-03
	 * The allowed results:
	 * 1) A module name. E.g.: «A_B».
	 * 2) A class name. E.g.: «A\B\C».
	 * 3) An object. It will be treated as case 2 after @see get_class()
	 * @used-by reportNamePrefix()
	 * @return string|object
	 */
	protected function module() {return $this;}

	/**
	 * Цель этого метода — предоставить потомкам возможность
	 * указывать тип предыдущей исключительной ситуации в комментарии PHPDoc для потомка.
	 * Метод @uses \Exception::getPrevious() объявлен как final,
	 * поэтому потомки не могут в комментариях PHPDoc указывать его тип: IntelliJ IDEA ругается.
	 * 2016-08-19
	 * @return E
	 */
	protected function prev() {return $this->getPrevious();}

	/**
	 * @used-by comments()
	 * @var string[]
	 */
	private $_comments = [];

	/**
	 * 2015-10-10
	 * @var array(string => mixed)
	 */
	private $_data = [];

	/**
	 * 2016-07-31
	 * @used-by isMessageHtml()
	 * @used-by markMessageAsHtml()
	 * @var bool
	 */
	private $_messageIsHtml = false;

	/**
	 * Количество последних элементов стека вызовов,
	 * которые надо пропустить как несущественные
	 * при показе стека вызовов в диагностическом отчёте.
	 * Это значение становится положительным,
	 * когда исключительная ситуация возбуждается не в момент её возникновения,
	 * а в некоей вспомогательной функции-обработчике, вызываемой в сбойном участке:
	 * @see \Justuno\Core\Qa\Method::throwException()
	 * @var int
	 */
	private $_stackLevelsCountToSkip = 0;

	/**
	 * @used-by ju_ewrap()
	 * @param \Exception $e
	 * @return $this
	 */
	static function wrap(E $e) {return $e instanceof self ? $e : new self($e);}
}