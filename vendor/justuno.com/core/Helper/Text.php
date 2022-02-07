<?php
namespace Justuno\Core\Helper;
# 2020-06-26 "Port the `Df\Core\Helper\Text` class": https://github.com/justuno-com/core/issues/160
class Text {
	/**
	 * @used-by ju_quote_double()
	 * @used-by ju_quote_russian()
	 * @used-by ju_quote_single()
	 * @param string|string[]|array(string => string) $s
	 * @param string $t [optional]
	 * @return string|string[]
	 */
	function quote($s, $t = self::QUOTE__RUSSIAN) {
		if ('"' === $t) {
			$t = self::QUOTE__DOUBLE;
		}
		elseif ("'" === $t) {
			$t = self::QUOTE__SINGLE;
		}
		static $m = [
			self::QUOTE__DOUBLE => ['"', '"'], self::QUOTE__RUSSIAN => ['«', '»'], self::QUOTE__SINGLE => ["'", "'"]
		]; /** @var array(string => string[]) $m */
		$quotes = jua($m, $t); /** @var string[] $quotes */
		if (!is_array($quotes)) {
			ju_error("An unknown quote: «{$t}».");
		}
		/**
		 * 2016-11-13 It injects the value $s inside quotes.
		 * @param string $s
		 * @return string
		 */
		$f = function($s) use($quotes) {return implode($s, $quotes);};
		return !is_array($s) ? $f($s) : array_map($f, $s);
	}

	/**
	 * 2015-03-03
	 * @used-by ju_extend()
	 * @param string $s
	 * @return string
	 */
	function singleLine($s) {return str_replace(["\r\n", "\r", "\n", "\t"], ' ', $s);}

	/**
	 * @used-by quote()
	 * @used-by df_quote_double()
	 */
	const QUOTE__DOUBLE = 'double';

	/**
	 * @used-by quote()
	 * @used-by df_quote_russian()
	 */
	const QUOTE__RUSSIAN = 'russian';

	/**
	 * @used-by quote()
	 * @used-by df_quote_single()
	 */
	const QUOTE__SINGLE = 'single';

	/**
	 * @used-by ju_t()
	 * @return self
	 */
	static function s() {static $r; return $r ? $r : $r = new self;}
}