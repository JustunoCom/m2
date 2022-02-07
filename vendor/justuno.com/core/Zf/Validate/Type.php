<?php
namespace Justuno\Core\Zf\Validate;
/**
 * 2020-06-22 "Port the `Df\Zf\Validate\Type` class": https://github.com/justuno-com/core/issues/111
 * @see \Justuno\Core\Zf\Validate\StringT
 * @see \Justuno\Core\Zf\Validate\StringT\IntT
 */
abstract class Type extends \Justuno\Core\Zf\Validate {
	/** @return string */
	abstract protected function getExpectedTypeInAccusativeCase();
	/** @return string */
	abstract protected function getExpectedTypeInGenitiveCase();

	/**
	 * @override
	 * @return string
	 */
	protected function getMessageInternal() {return
		is_null($this->getValue()) ? $this->getDiagnosticMessageForNull() : $this->getDiagnosticMessageForNotNull()
	;}

	/** @return string */
	private function getDiagnosticMessageForNotNull() {return strtr(
		'Unable to recognize the value «{value}» of type «{type}» as {expected type}.', [
			'{value}' => ju_string_debug($this->getValue()),
			'{type}' => gettype($this->getValue()),
			'{expected type}' => $this->getExpectedTypeInAccusativeCase()
		]
	);}

	/** @return string */
	private function getDiagnosticMessageForNull() {return
		"Got `NULL` instead of {$this->getExpectedTypeInGenitiveCase()}.";
	}
}