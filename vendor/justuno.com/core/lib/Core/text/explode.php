<?php
/**
 * «YandexMarket» => array(«Yandex», «Market»)
 * «NewNASAModule» => array(«New», «NASA», «Module»)
 * http://stackoverflow.com/a/17122207
 *
 * 2016-08-24
 * http://php.net/manual/reference.pcre.pattern.modifiers.php
 * x (PCRE_EXTENDED)
 * 		If this modifier is set, whitespace data characters in the pattern are totally ignored
 * 		except when escaped or inside a character class,
 * 		and characters between an unescaped # outside a character class
 * 		and the next newline character, inclusive, are also ignored.
 *
 * 		This is equivalent to Perl's /x modifier,
 * 		and makes it possible to include commentary inside complicated patterns.
 *
 * 		Note, however, that this applies only to data characters.
 * 		Whitespace characters may never appear within special character sequences in a pattern,
 * 		for example within the sequence (?( which introduces a conditional subpattern.
 *
 * 2017-07-09
 * Note 1: ?<=
 * «Zero-width positive lookbehind assertion.
 * Continues match only if the subexpression matches at this position on the left.
 * For example, (?<=19)99 matches instances of 99 that follow 19.
 * This construct does not backtrack.»
 *
 * Note 2: ?=
 * «Zero-width positive lookahead assertion.
 * Continues match only if the subexpression matches at this position on the right.
 * For example, \w+(?=\d) matches a word followed by a digit, without matching the digit.
 * This construct does not backtrack.»
 *
 * I have extracted this explanation from Rad Software Regular Expression Designer
 * (it is a discontinued software, google for it),
 * and it get it from the .NET Framework 3.0 documentation:
 * https://msdn.microsoft.com/en-us/library/bs2twtah(v=vs.85).aspx
 *
 * Note 3.
 * Today I have changed «?=[A-Z0-9]» => «?=[A-Z0-9]», so now it handles the cases with digits, e.g.:
 * «Dynamics365» => [«Dynamics», «365»]
 *
 * 2020-08-21 "Port the `ju_explode_camel` function" https://github.com/justuno-com/core/issues/221
 *
 * @used-by ju_explode_class_camel()
 * @param string ...$args
 * @return string[]|string[][]
 */
function ju_explode_camel(...$args) {return ju_call_a(function($name) {return preg_split(
	'#(?<=[a-z])(?=[A-Z0-9])#x', $name
);}, $args);}

/**
 * 2016-03-25 «charge.dispute.funds_reinstated» => [charge, dispute, funds, reinstated]
 * 2020-06-26 "Port the `df_explode_multiple` function": https://github.com/justuno-com/core/issues/140
 * @used-by ju_explode_class()
 * @param string[] $delimiters
 * @param string $s
 * @return string[]
 */
function ju_explode_multiple(array $delimiters, $s) {
	$main = array_shift($delimiters); /** @var string $main */
	/**
	 * 2016-03-25
	 * «If search is an array and replace is a string,
	 * then this replacement string is used for every value of search.»
	 * http://php.net/manual/function.str-replace.php
	 */
	return explode($main, str_replace($delimiters, $main, $s));
}

/**
 * 2018-04-24 I have added @uses trim() today.
 * 2020-06-20 "Port the `df_explode_n` function": https://github.com/justuno-com/core/issues/86
 * @used-by ju_tab_multiline()
 * @param string $s
 * @return string[]
 */
function ju_explode_n($s) {return explode("\n", ju_normalize(ju_trim($s)));}

/**
 * 2016-09-03 Another implementation: df_explode_multiple(['/', DS], $path)
 * 2021-03-07 "Port the `df_explode_path` function": https://github.com/justuno-com/core/issues/368
 * @used-by ju_url_trim_index()
 * @param string $p
 * @return string[]
 */
function ju_explode_path($p) {return ju_explode_xpath(ju_path_n($p));}

/**
 * 2020-06-14 "Port the `df_explode_xpath` function": https://github.com/justuno-com/core/issues/20
 * @used-by ju_explode_path()
 * @used-by jua_deep()
 * @used-by jua_deep_set()
 * @used-by jua_deep_unset()
 * @used-by \Justuno\Core\Config\Backend::value()
 * @param string|string[] $p
 * @return string[]
 */
function ju_explode_xpath($p) {return jua_flatten(array_map(function($s) {return explode('/', $s);}, ju_array($p)));}