<?php
namespace Justuno\Core\Qa\Trace;
use ReflectionFunction as RF;
use ReflectionFunctionAbstract as RFA;
use ReflectionMethod as RM;
use ReflectionParameter as RP;
# 2020-08-19 "Port the `Df\Qa\Trace\Frame` class" https://github.com/justuno-com/core/issues/197
final class Frame extends \Justuno\Core\O {
	/**
	 * @used-by \Justuno\Core\Qa\Trace\Formatter::frame()
	 * @return string
	 */
	function context() {return juc($this, function() {
		$r = ''; /** @var string $r */
		if (is_file($this->filePath()) && $this->line()) {
			$fileContents = file($this->filePath());/** @var string[] $fileContents */
			if (is_array($fileContents)) {
				$fileLength = count($fileContents); /** @var int $fileLength */
				$radius = 8; /** @var int $radius */
				$start = max(0, $this->line() - $radius); /** @var int $start */
				$end = min($fileLength, $start + 2 * $radius); /** @var int $end */
				if ($this->_next) { # 2016-07-31 Нам нужна информация именно функции next (caller).
					$func = $this->_next->functionA(); /** @var RFA|null $func */
					/**
					 * 2016-07-31
					 * Если @uses \ReflectionFunctionAbstract::isInternal() вернёт true,
					 * то @uses \ReflectionFunctionAbstract::getStartLine() и
					 * @uses \ReflectionFunctionAbstract::getEndLine() вернут false.
					 * http://stackoverflow.com/questions/2222142#comment25428181_2222404
					 * isInternal() === TRUE means ->getFileName() and ->getStartLine() will return FALSE
					 */
					if ($func && !$func->isInternal()) {
						$fStart = ju_assert_nef($func->getStartLine()); /** @var int $fStart */
						$fEnd = ju_assert_nef($func->getEndLine()); /** @var int $fEnd */
						# 2016-07-31
						# http://stackoverflow.com/a/7027198
						# It's actually - 1, otherwise you wont get the function() block.
						$start = max($start, $fStart - 1);
						$end = min($end, $fEnd);
					}
				}
				$r = ju_trim(implode(array_slice($fileContents, $start, $end - $start)));
			}
		}
		return $r;
	});}

	/**
	 * 2015-04-03 Путь к файлу отсутствует при вызовах типа @see call_user_func()
	 * @used-by context()
	 * @used-by \Justuno\Core\Qa\Trace\Formatter::frame()
	 * @return string|null
	 */
	function filePath() {return $this['file'];}

	/**
	 * 2015-04-03 Строка отсутствует при вызовах типа @see call_user_func()
	 * @used-by context()
	 * @used-by \Justuno\Core\Qa\Trace\Formatter::frame()
	 * @return int|null
	 */
	function line() {return $this['line'];}

	/**
	 * 2020-02-20
	 * $f could be `include`, `include_once`, `require`, ``require_once``:
	 * https://www.php.net/manual/function.include.php
	 * https://www.php.net/manual/function.include-once.php
	 * https://www.php.net/manual/function.require.php
	 * https://www.php.net/manual/function.require-once.php
	 * https://www.php.net/manual/function.debug-backtrace.php#111255
	 * They are not functions and will lead to a @see \ReflectionException:
	 * «Function include() does not exist»: https://github.com/tradefurniturecompany/site/issues/60
	 * https://www.php.net/manual/reflectionfunction.construct.php
	 * https://www.php.net/manual/class.reflectionexception.php
	 * @see functionA()
	 * @used-by functionA()
	 * @used-by methodParameter()
	 * @used-by \Justuno\Core\Qa\Method::raiseErrorParam()
	 * @return RM|null
	 */
	function method() {return juc($this, function() {return
		($c = $this->className()) && ($f = $this->functionName()) && !$this->isClosure()
			? ju_try(function() use($c, $f) {return new RM($c, $f);}, null)
			: null
	;});}

	/**
	 * 2015-04-03 Для простых функций (не методов) вернёт название функции.
	 * @used-by __toString()
	 * @used-by methodParameter()
	 * @used-by \Justuno\Core\Qa\Method::raiseErrorParam()
	 * @used-by \Justuno\Core\Qa\Method::raiseErrorResult()
	 * @used-by \Justuno\Core\Qa\Method::raiseErrorVariable()
	 * @return string
	 */
	function methodName() {return ju_cc_method($this->className(), $this->functionName());}

	/**
	 * @used-by \Justuno\Core\Qa\Method::raiseErrorParam()
	 * @param int $ordering  		zero-based
	 * @return RP
	 */
	function methodParameter($ordering) {return juc($this, function($ordering) {/** @var RP $r */
		ju_assert($m = $this->method()); /** @var RM|null $m */
		if ($ordering >= count($m->getParameters())) { # Параметр должен существовать
			ju_error(
				"Программист ошибочно пытается получить значение параметра с индексом {$ordering}"
				." метода «{$this->methodName()}», хотя этот метод принимает всего %d параметров."
				,count($m->getParameters())
			);
		}
		ju_assert_lt(count($m->getParameters()), $ordering);
		ju_assert(($r = jua($m->getParameters(), $ordering)) instanceof RP);
		return $r;
	}, [$ordering]);}

	/**
	 * 2020-02-27
	 * @used-by __toString()
	 * @used-by i()
	 * @used-by \Justuno\Core\Qa\Trace\Formatter::p()
	 * @param string $v
	 * @return bool|null
	 */
	function showContext($v = JU_N) {return ju_prop($this, $v);}

	/**
	 * @used-by method()
	 * @used-by methodName()
	 * @return string
	 */
	private function className() {return ju_nts($this['class']);}

	/**
	 * 2016-07-31 Без проверки на closure будет сбой: «Function Df\Config\{closure}() does not exist».
	 * 2020-02-20
	 * $f could be `include`, `include_once`, `require`, ``require_once``:
	 * https://www.php.net/manual/function.include.php
	 * https://www.php.net/manual/function.include-once.php
	 * https://www.php.net/manual/function.require.php
	 * https://www.php.net/manual/function.require-once.php
	 * https://www.php.net/manual/function.debug-backtrace.php#111255
	 * They are not functions and will lead to a @see \ReflectionException:
	 * «Function include() does not exist»: https://github.com/tradefurniturecompany/site/issues/60
	 * https://www.php.net/manual/reflectionfunction.construct.php
	 * https://www.php.net/manual/class.reflectionexception.php
	 * @see method()
	 * @used-by context()
	 * @return RFA|RF|RM|null
	 */
	private function functionA() {return juc($this, function() {return $this->method() ?: (
		(!($f = $this->functionName())) || $this->isClosure() ? null : ju_try(function() use($f) {return new RF($f);}, null)
	);});}
	
	/**
	 * @used-by method()
	 * @used-by methodName()
	 * @return string
	 */
	private function functionName() {return ju_nts($this['function']);}

	/**
	 * 2016-07-31
	 * @used-by functionA()
	 * @used-by method()
	 * @return bool
	 */
	private function isClosure() {return ju_ends_with($this->functionName(), '{closure}');}

	/**
	 * @used-by context()
	 * @used-by i()
	 * @var self|null
	 */
	private $_next;

	/**           
	 * 2020-02-27 `self $previous` works even in PHP 5.0.0: https://3v4l.org/pTl8l
	 * @used-by \Justuno\Core\Qa\Method::caller()
	 * @used-by \Justuno\Core\Qa\Message\Failure::frames()
	 * @param array(string => string|int) $frameA
	 * @param self|null $previous [optional]
	 * @param bool $showContext [optional]
	 * @return self
	 */
	static function i(array $frameA, self $previous = null, $showContext = false) { /** @var self $r */
		$r = new self($frameA);
		$r->showContext($showContext);
		if ($previous) {
			$previous->_next = $r;
		}
		return $r;
	}
}