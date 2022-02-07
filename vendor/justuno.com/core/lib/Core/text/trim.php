<?php
/**
 * 2017-06-09
 * 2021-02-22
 * @param string $s
 * @param int|null $max [optional]
 * @return string
 */
function ju_chop($s, $max = null) {return !$max || (mb_strlen($s = ju_trim($s)) <= $max) ? $s :
	ju_trim_right(mb_substr($s, 0, $max - 1)) . '…'
;}

/**
 * 2020-06-20 "Port the `df_trim` function": https://github.com/justuno-com/core/issues/88
 * @used-by ju_chop()
 * @used-by ju_csv_parse()
 * @used-by ju_explode_n()
 * @used-by ju_trim()
 * @used-by ju_trim_ds()
 * @used-by \Justuno\Core\Format\Html\Tag::content()
 * @used-by \Justuno\Core\Qa\Message::sections()
 * @used-by \Justuno\Core\Qa\Trace\Frame::context()
 * @param string|string[] $s
 * @param string|null $charlist [optional]
 * @param bool|mixed|\Closure $throw [optional]
 * @return string|string[]
 */
function ju_trim($s, $charlist = null, $throw = false) {return ju_try(function() use($s, $charlist, $throw) {
	/** @var string|string[] $r */
	if (is_array($s)) {
		$r = ju_map('ju_trim', $s, [$charlist, $throw]);
	}
	else {
		if (!is_null($charlist)) {
			/** @var string[] $addionalSymbolsToTrim */
			$addionalSymbolsToTrim = ["\n", "\r", ' '];
			foreach ($addionalSymbolsToTrim as $addionalSymbolToTrim) {
				/** @var string $addionalSymbolToTrim */
				if (!ju_contains($charlist, $addionalSymbolToTrim)) {
					$charlist .= $addionalSymbolToTrim;
				}
			}
		}
		/** @var \Justuno\Core\Zf\Filter\StringTrim $filter */
		$filter = new \Justuno\Core\Zf\Filter\StringTrim($charlist);
		$r = $filter->filter($s);
		$r = ju_nts($r);
		if (' ' === $r) {
			$r = '';
		}
	}
	return $r;
}, false === $throw ? $s : $throw);}

/**
 * 2017-08-18 Today I have noticed that $charlist = null does not work for @uses ltrim()
 * 2020-08-13 "Port the `df_trim_left` function" https://github.com/justuno-com/core/issues/176
 * @used-by ju_trim_ds_left()
 * @used-by \Justuno\Core\Config\Settings::phpNameToKey()
 * @param string $s
 * @param string $charlist [optional]
 * @return string
 */
function ju_trim_left($s, $charlist = null) {return ltrim($s, $charlist ?: " \t\n\r\0\x0B");}

/**
 * 2017-08-18 Today I have noticed that $charlist = null does not work for @uses rtrim()
 * 2020-06-21 "Port the `df_trim_right` function": https://github.com/justuno-com/core/issues/98
 * @used-by ju_chop()
 * @used-by ju_file_ext_def()
 * @param string $s
 * @param string $charlist [optional]
 * @return string
 */
function ju_trim_right($s, $charlist = null) {return rtrim($s, $charlist ?: " \t\n\r\0\x0B");}

/**
 * 2016-10-28
 * 2020-06-25 "Port the `df_trim_text_a` function": https://github.com/justuno-com/core/issues/136
 * @used-by ju_trim_text_left()
 * @used-by ju_trim_text_right()
 * @param string $s
 * @param string[] $trimA
 * @param callable $f
 * @return string
 */
function ju_trim_text_a($s, array $trimA, callable $f) {
	$r = $s; /** @var string $r */
	$l = mb_strlen($r); /** @var int $l */
	foreach ($trimA as $trim) {/** @var string $trim */
		if ($l !== mb_strlen($r = call_user_func($f, $r, $trim))) {
			break;
		}
	}
	return $r;
}

/**
 * It chops the $trim prefix from the $s string.
 * 2016-10-28 It now supports multiple $trim.
 * 2020-06-24 "Port the `df_trim_text_left` function": https://github.com/justuno-com/core/issues/135
 * @used-by ju_domain()
 * @used-by ju_magento_version()
 * @used-by ju_oqi_amount()
 * @used-by ju_path_relative()
 * @param string $s
 * @param string|string[] $trim
 * @return string
 */
function ju_trim_text_left($s, $trim) {return is_array($trim) ? ju_trim_text_a($s, $trim, __FUNCTION__) : (
	$trim === mb_substr($s, 0, $l = mb_strlen($trim)) ? mb_substr($s, $l) : $s
);}

/**
 * It chops the $trim ending from the $s string.
 * 2016-10-28 It now supports multiple $trim.
 * 2020-06-26 "Port the `df_trim_text_right` function": https://github.com/justuno-com/core/issues/142
 * @used-by ju_cts()
 * @used-by ju_oqi_amount()
 * @param string $s
 * @param string|string[] $trim
 * @return string
 */
function ju_trim_text_right($s, $trim) {return is_array($trim) ? ju_trim_text_a($s, $trim, __FUNCTION__) : (
	0 !== ($l = mb_strlen($trim)) && $trim === mb_substr($s, -$l) ? mb_substr($s, 0, -$l) : $s
);}