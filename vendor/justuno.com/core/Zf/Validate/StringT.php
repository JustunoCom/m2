<?php
namespace Justuno\Core\Zf\Validate;
use Magento\Framework\Phrase;
# 2020-06-22 "Port the `Df\Zf\Validate\StringT` class": https://github.com/justuno-com/core/issues/110
class StringT extends Type implements \Zend_Filter_Interface {
	/**
	 * @override
	 * @param mixed $v
	 * @throws \Zend_Filter_Exception
	 * @return string|mixed
	 */
	function filter($v) {return is_null($v) || is_int($v) ? strval($v) : $v;}

	/**
	 * @override
	 * @see \Zend_Validate_Interface::isValid()
	 * @used-by ju_check_s()
	 * @param mixed $v
	 * @return bool
	 */
	function isValid($v) {
		$this->prepareValidation($v);
		return is_string($v) || is_int($v) || is_null($v) || is_bool($v) || $v instanceof Phrase;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInAccusativeCase() {return 'строку';}

	/**
	 * @override
	 * @return string
	 */
	protected function getExpectedTypeInGenitiveCase() {return 'строки';}

	/** @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}