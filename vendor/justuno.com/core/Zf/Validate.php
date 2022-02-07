<?php
namespace Justuno\Core\Zf;
# 2020-06-22 "Port the `Df\Zf\Validate` class": https://github.com/justuno-com/core/issues/112
abstract class Validate implements \Zend_Validate_Interface {
	/** @return string */
	abstract protected function getMessageInternal();

	/** @param array(string => mixed) $params */
	function __construct(array $params = []) {$this->_params = $params;}

	/**
	 * @deprecated Since 1.5.0
	 * @override
	 * @return array(string => string)
	 */
	function getErrors() {return array_keys($this->getMessages());}

	/**
	 * @override
	 * @return string
	 */
	function getMessage() {
		if (!isset($this->_message)) {
			$this->_message = $this->getMessageInternal();
			if ($this->getExplanation()) {
				$this->_message .= ("\n" . $this->getExplanation());
			}
		}
		return $this->_message;
	}

	/**
	 * @override
	 * @return array(string => string)
	 */
	function getMessages() {return [__CLASS__ => $this->getMessage()];}

	/**
	 * @param string $paramName
	 * @param mixed $d [optional]
	 * @return mixed
	 */
	final protected function cfg($paramName, $d = null) {return jua($this->_params, $paramName, $d);}

	/** @return string|null */
	protected function getExplanation() {return $this->cfg(self::$PARAM__EXPLANATION);}

	/** @return mixed */
	protected function getValue() {return $this->cfg(self::$PARAM__VALUE);}

	/**
	 * @param mixed $v
	 */
	protected function prepareValidation($v) {$this->setValue($v);}

	/** @used-by setValue() */
	protected function reset() {
		unset($this->_message);
		unset($this->_params[self::$PARAM__VALUE]);
		unset($this->_params[self::$PARAM__EXPLANATION]);
	}

	/**
	 * @param string $value
	 */
	protected function setExplanation($value) {$this->_params[self::$PARAM__EXPLANATION] = $value;}

	/**
	 * @param string $message
	 */
	protected function setMessage($message) {$this->_message = $message;}

	/**
	 * @param mixed $value
	 */
	private function setValue($value) {
		$this->reset();
		$this->_params[self::$PARAM__VALUE] = $value;
	}

	/** @var string */
	private $_message;
	/** @var array(string => mixed) */
	private $_params = [];

	/** @var string */
	private static $PARAM__EXPLANATION = 'explanation';
	/** @var string */
	private static $PARAM__VALUE = 'value';
}