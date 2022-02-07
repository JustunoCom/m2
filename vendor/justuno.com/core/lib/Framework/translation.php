<?php
use Magento\Framework\Phrase as P;
/**
 * 2016-07-14
 * 2021-03-06 "Port the `df_phrase` function": https://github.com/justuno-com/core/issues/353
 * @used-by ju_message_add()
 * @param P|string $s
 * @return P
 */
function ju_phrase($s) {return $s instanceof P ? $s : __($s);}

/**
 * 2015-09-29
 * 2020-08-22 "Port the `ju_translate_a` function" https://github.com/justuno-com/core/issues/262
 * @used-by ju_map_to_options_t()
 * @param string[] $strings
 * @param bool $now [optional]
 * @return string[]
 */
function ju_translate_a($strings, $now = false) {
	$r = array_map('__', $strings); /** @var string[] $r */
	return !$now ? $r : array_map('strval', $r);
}

/**
 * 2017-02-09
 * It does the same as @see \Magento\Framework\Filter\TranslitUrl::filter(), but without lower-casing:
 * '歐付寶 all/Pay' => 'all-Pay'
 * If you need lower-casing, then use @see df_translit_url_lc() instead.
 * 2020-08-13 "Port the `df_translit_url` function" https://github.com/justuno-com/core/issues/168
 *
 * Example #1: '歐付寶 all/Pay':
 * @see df_fs_name => 歐付寶-allPay
 * @see df_translit =>  all/Pay
 * @see ju_translit_url => all-Pay
 * @see df_translit_url_lc => all-pay
 *
 * Example #2: '歐付寶 O'Pay (allPay)':
 * @see df_fs_name => 歐付寶-allPay
 * @see df_translit =>  allPay
 * @see ju_translit_url => allPay
 * @see df_translit_url_lc => allpay
 *
 * @used-by df_translit_url_lc()
 * @used-by \Df\Sentry\Client::tags()
 * @param string $s
 * @return string
 */
function ju_translit_url($s) {return trim(preg_replace('#[^0-9a-z]+#i', '-', ju_translit($s)), '-');}