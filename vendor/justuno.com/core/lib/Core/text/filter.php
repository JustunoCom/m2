<?php
/**
 * 2019-01-11
 * 2019-11-15 https://stackoverflow.com/a/1253417
 * 2020-08-23 "Port the `df_is_guid` function" https://github.com/justuno-com/core/issues/297
 * @used-by \Justuno\M2\Block\Js::_toHtml()
 * @param string $s
 * @return bool
 */
function ju_is_guid($s) {return 36 === strlen($s) && preg_match(
	'#^\{?[A-Z0-9]{8}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{12}\}?$#', $s
);}

/**
 * 2020-06-20 "Port the `df_normalize` function": https://github.com/justuno-com/core/issues/87
 * http://darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
 * @used-by ju_explode_n()
 * @param string $s
 * @return string
 */
function ju_normalize($s) {return strtr($s, ["\r\n" => "\n", "\r" => "\n"]);}

/**
 * 2017-02-09
 * '歐付寶 all/Pay' => 'all/Pay'
 *
 * Example #1: '歐付寶 all/Pay':
 * @see df_fs_name => 歐付寶-allPay
 * @see ju_translit =>  all/Pay
 * @see ju_translit_url => all-Pay
 * @see df_translit_url_lc => all-pay
 *
 * Example #2: '歐付寶 O'Pay (allPay)':
 * @see df_fs_name => 歐付寶-allPay
 * @see ju_translit =>  allPay
 * @see ju_translit_url => allPay
 * @see df_translit_url_lc => allpay
 *
 * 2017-11-13
 * Note 1.
 * Previously, I used @see \Magento\Framework\Filter\Translit::filter() here:
 * 		$m = df_o(Translit::class); return $m->filter($s);
 * https://github.com/mage2pro/core/blob/3.3.9/Framework/lib/translation.php#L32-L56
 * But now I decided to switch to @uses transliterator_transliterate(),
 * because it produces better results for me.
 *
 * $m = df_o(Translit::class);
 * echo $m->filter('歐付寶 Rónán allPay Федюк [] --');
 * Output: «Ronan allPay fedjuk [] --»
 *
 * echo transliterator_transliterate('Any-Latin; Latin-ASCII', '歐付寶 Rónán allPay Федюк [] --')
 * Output: «ou fu bao Ronan allPay Feduk [] --»
 *
 * Note 2.
 * I have already implemented a similar algorithm in JavaScript:
 * https://github.com/mage2pro/core/blob/3.2.9/Payment/view/frontend/web/card.js#L188-L201
 *		baChange(this, function(a) {this.cardholder((a.firstname + ' ' + a.lastname).toUpperCase()
 *			.normalize('NFD').replace(/[^\w\s-]/g, '')
 *		);});
 * https://github.com/mage2pro/core/issues/37#issuecomment-337546667
 *
 * Note 3. I have adapted an implementation from here: https://stackoverflow.com/questions/3371697#comment63507856_3371773
 *
 * 2020-08-13 "Port the `df_translit` function" https://github.com/justuno-com/core/issues/169
 *
 * @used-by ju_translit_url()
 * @param string $s
 * @return string
 */
function ju_translit($s) {return transliterator_transliterate('Any-Latin; Latin-ASCII', $s);}