<?php
/**
 * 2016-03-08 It adds the $tail suffix to the $s string if the suffix is absent in $s.
 * 2020-08-22 "Port the `df_append` function" https://github.com/justuno-com/core/issues/241
 * @used-by ju_file_ext_add()
 * @param string $s
 * @param string $tail
 * @return string
 */
function ju_append($s, $tail) {return ju_ends_with($s, $tail) ? $s : $s . $tail;}

/**
 * 2020-06-18 "Port the `df_pad` function": https://github.com/justuno-com/core/issues/64
 * @used-by ju_kv()
 * @used-by \Justuno\Core\Qa\Trace\Formatter::param()
 * @param string $phrase
 * @param int $length
 * @param string $pattern
 * @param int $position
 * @return string
 */
function ju_pad($phrase, $length, $pattern = ' ', $position = STR_PAD_RIGHT) {/** @var string $r */
	$encoding = 'UTF-8'; /** @var string $encoding */
	$input_length = mb_strlen($phrase, $encoding); /** @var int $input_length */
	$pad_string_length = mb_strlen($pattern, $encoding); /** @var int $pad_string_length */
	if ($length <= 0 || $length - $input_length <= 0) {
		$r = $phrase;
	}
	else {
		$num_pad_chars = $length - $input_length; /** @var int $num_pad_chars */
		/** @var int $left_pad */ /** @var int $right_pad */
		switch ($position) {
			case STR_PAD_RIGHT:
				list($left_pad, $right_pad) = [0, $num_pad_chars];
				break;
			case STR_PAD_LEFT:
				list($left_pad, $right_pad) = [$num_pad_chars, 0];
				break;
			case STR_PAD_BOTH:
				$left_pad = floor($num_pad_chars / 2);
				$right_pad = $num_pad_chars - $left_pad;
				break;
			default:
				ju_error();
				break;
		}
		$r = '';
		for ($i = 0; $i < $left_pad; ++$i) {
			$r .= mb_substr($pattern, $i % $pad_string_length, 1, $encoding);
		}
		$r .= $phrase;
		for ($i = 0; $i < $right_pad; ++$i) {
			$r .= mb_substr($pattern, $i % $pad_string_length, 1, $encoding);
		}
	}
	return $r;
}

/**
 * 2016-03-08 It adds the $head prefix to the $s string if the prefix is absent in $s.
 * @used-by \Justuno\Core\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetElementHtml()
 * @param string $s
 * @param string $head
 * @return string
 */
function ju_prepend($s, $head) {return ju_starts_with($s, $head) ? $s : $head . $s;}

/**
 * 2020-06-18 "Port the `df_tab` function": https://github.com/justuno-com/core/issues/85
 * @used-by ju_tab_multiline()
 * @param string ...$args
 * @return string|string[]|array(string => string)
 */
function ju_tab(...$args) {return ju_call_a(function($text) {return "\t" . $text;}, $args);}

/**
 * 2020-06-18 "Port the `df_tab_multiline` function": https://github.com/justuno-com/core/issues/84
 * @used-by \Justuno\Core\Format\Html\Tag::content()
 * @used-by \Justuno\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * @used-by \Justuno\Core\Qa\Dumper::dumpArray()
 * @param string $s
 * @return string
 */
function ju_tab_multiline($s) {return ju_cc_n(ju_tab(ju_explode_n($s)));}