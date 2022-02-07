<?php
namespace Justuno\Core\Qa\Trace;
use Justuno\Core\Qa\Trace as T;
use Justuno\Core\Qa\Trace\Frame as F;
# 2020-02-27
# 2020-08-19 "Port the `Df\Qa\Trace\Formatter` class" https://github.com/justuno-com/core/issues/196
final class Formatter {
	/**
	 * 2020-02-27
	 * @used-by ju_bt_s()
	 * @used-by \Justuno\Core\Qa\Message\Failure::postface()
	 * @param T $t
	 * @param bool $showContext [optional]
	 * @return string
	 */
	static function p(T $t, $showContext = false) {return jucf(function($t, $showContext) {
		$count = count($t); /** @var int $count */
		return implode(ju_map_k($t, function($index, F $frame) use($count, $showContext) {
			$index++;
			$frame->showContext($showContext);
			$r = self::frame($frame); /** @var string $r */
			if ($index !== $count) {
				$indexS = (string)$index; /** @var string $indexS */
				$indexLength = strlen($indexS); /** @var int $indexLength */
				$delimiterLength = 36; /** @var int $delimiterLength */
				$fillerLength = $delimiterLength - $indexLength; /** @var int $fillerLength */
				$fillerLengthL = floor($fillerLength / 2); /** @var int $fillerLengthL */
				$fillerLengthR = $fillerLength - $fillerLengthL; /** @var int $fillerLengthR */
				$r .= "\n" . str_repeat('*', $fillerLengthL) . $indexS . str_repeat('*', $fillerLengthR) . "\n";
			}
			return $r;
		}));
	}, [$t, $showContext]);}

	/**     
	 * 2020-02-27          
	 * @used-by p()
	 * @param Frame $f 
	 * @return string
	 */
	private static function frame(F $f) {/** @var string $r */
		try {
			$resultA = array_filter(array_map([__CLASS__, 'param'], [
				['Location', ju_cc(':', ju_path_relative($f->filePath()), $f->line())], ['Callee', $f->methodName()]
			])); /** @var string[] $resultA */ /** @uses param() */
			if ($f->showContext() && $f->context()) {
				$resultA[]= self::param(['Context', "\n{$f->context()}"]);
			}
			$r = ju_cc_n($resultA);
		}
		catch (\Exception $e) {
			$r = ju_ets($e);
			/**
			 * 2020-02-20
			 * 1) «Function include() does not exist»: https://github.com/tradefurniturecompany/site/issues/60
			 * 2) It is be dangerous to call @see ju_log_e() here, because it will inderectly return us here,
			 * and it could be an infinite loop.
			 */
			static $loop = false;
			if ($loop) {
				ju_log_l(__CLASS__, "$r\n{$e->getTraceAsString()}", ju_class_l(__CLASS__));
			}
			else {
				$loop = true;
				ju_log_e($e, __CLASS__);
				$loop = false;
			}
		}
		return $r;		
	}
	
	/**
	 * @used-by p()
	 * @param array $p
	 * @return string|null
	 */
	private static function param(array $p) {/** @var string|null $r */ /** @var string|null $v */
		if (!($v = $p[1])) {
			$r = null;
		}
		else {
			$label = $p[0]; /** @var string $label */
			$pad = ju_pad(' ', 12 - mb_strlen($label)); /** @var string $pad */
			$r = "{$label}:{$pad}{$v}";
		}
		return $r;
	}	
}