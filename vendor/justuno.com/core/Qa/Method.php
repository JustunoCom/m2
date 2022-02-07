<?php
namespace Justuno\Core\Qa;
use Justuno\Core\Qa\Trace\Frame;
use Justuno\Core\Zf\Validate\StringT as VString;
use Exception as E;
use ReflectionParameter as RP;
use Zend_Validate_Interface as Vd;
# 2020-06-20 "Port the `Df\Qa\Method` class": https://github.com/justuno-com/core/issues/95
final class Method {
	/**
	 * @used-by ju_assert_sne()
	 * @used-by ju_param_s()
	 * @used-by ju_param_sne()
	 * @param string $v
	 * @param int $sl [optional]
	 * @return string
	 * @throws E
	 */
	static function assertValueIsString($v, $sl = 0) {return self::vv(VString::s(), $v, ++$sl);}

	/**
	 * @used-by ju_param_s()
	 * @used-by ju_param_sne()
	 * @used-by vp()
	 * @param string $method
	 * @param array $messages
	 * @param int $ord  zero-based
	 * @param int $sl
	 * @throws E
	 */
	static function raiseErrorParam($method, array $messages, $ord, $sl = 1) {
		$frame = self::caller($sl); /** @var Frame $frame */
		$name = 'unknown'; /** @var string $name */
		if (!is_null($ord) && $frame->method()) {/** @var RP $param */
			$name = $frame->methodParameter($ord)->getName();
		}
		$messagesS = ju_cc_n($messages); /** @var string $messagesS */
		self::throwException(
			"[{$frame->methodName()}]"
			."\nThe argument «{$name}» is rejected by the «{$method}» validator."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			,$sl
		);
	}

	/**
	 * @used-by ju_result_s()
	 * @used-by ju_result_sne()
	 * @used-by vr()
	 * @param string $vd
	 * @param array $messages
	 * @param int $sl
	 * @throws E
	 */
	static function raiseErrorResult($vd, array $messages, $sl = 1) {
		$messagesS = ju_cc_n($messages); /** @var string $messagesS */
		$method = self::caller($sl)->methodName(); /** @var string $method */
		self::throwException(
			"[{$method}]\nA result of this method is rejected by the «{$vd}» validator."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			, $sl
		);
	}

	/**
	 * @used-by ju_assert_sne()
	 * @used-by vv()
	 * @param string $vd
	 * @param array $messages
	 * @param int $sl
	 * @throws E
	 */
	static function raiseErrorVariable($vd, array $messages, $sl = 1) {
		$messagesS = ju_cc_n($messages); /** @var string $messagesS */
		$method = self::caller($sl)->methodName(); /** @var string $method */
		self::throwException(
			"[{$method}]\nThe validator «{$vd}» has catched a variable with an invalid value."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			, $sl
		);
	}

	/**
	 * 2017-01-12
	 * @used-by ju_assert_sne()
	 * @used-by ju_param_sne()
	 * @used-by ju_result_sne()
	 */
	const NES = 'A non-empty string is required, but got an empty one.';

	/**
	 * 2017-04-22
	 * @used-by ju_param_s()
	 */
	const S = 'A string is required.';

	/**
	 * @used-by raiseErrorParam()
	 * @used-by raiseErrorResult()
	 * @used-by raiseErrorVariable()
	 * @param int $offset [optional]
	 * @return Frame
	 */
	private static function caller($offset) {return Frame::i(
		debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3 + $offset)[2 + $offset]
	);}

	/**
	 * 2015-01-28
	 * @param string $message
	 * @param int $sl [optional]
	 * @throws E
	 */
	private static function throwException($message, $sl = 0) {ju_error(new E($message, ++$sl));}
	
	/**
	 * @param Vd $vd
	 * @param mixed $v
	 * @param int $ord
	 * @param int $sl
	 * @return mixed
	 * @throws E
	 */
	private static function vp(Vd $vd, $v, $ord, $sl = 1) {return $vd->isValid($v) ? $v : self::raiseErrorParam(
		get_class($vd), $vd->getMessages(), $ord, ++$sl
	);}

	/**
	 * @param Vd $vd
	 * @param mixed $v
	 * @param int $sl
	 * @return mixed
	 * @throws E
	 */
	private static function vr(Vd $vd, $v, $sl = 1) {return $vd->isValid($v) ? $v : self::raiseErrorResult(
		get_class($vd), $vd->getMessages(), ++$sl
	);}
	
	/**
	 * @param Vd $vd
	 * @param mixed $v
	 * @param int $sl
	 * @return mixed
	 * @throws E
	 */
	private static function vv(Vd $vd, $v, $sl = 1) {return $vd->isValid($v) ? $v : self::raiseErrorVariable(
		get_class($vd), $vd->getMessages(), ++$sl
	);}
}