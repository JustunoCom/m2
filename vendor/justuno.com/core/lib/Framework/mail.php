<?php
use Magento\Email\Model\Transport;
use Magento\Framework\Mail\Message as Msg;
use Magento\Framework\Mail\TransportInterface as ITransport;

/**
 * 2019-06-13
 * 2021-03-07 "Port the `df_mail` function": https://github.com/justuno-com/core/issues/360
 * 2021-08-05 @deprecated It is unused.
 * @param string|string[] $to
 * @param string $subject
 * @param string $body
 */
function ju_mail($to, $subject, $body) {
	$msg = ju_new_om(Msg::class); /** @var Msg $msg */
	ju_map(function($to) use($msg) {
		$msg->addTo($to);
	}, jua_flatten(array_map('ju_csv_parse', is_array($to) ? $to : [$to]))); /** @uses ju_csv_parse() */
	$msg
		->setBodyHtml($body)
		->setFrom(ju_cfg('trans_email/ident_general/email'))
		->setSubject($subject)
	;
	$t = ju_new_om(ITransport::class, ['message' => $msg]); /** @var ITransport|Transport $t */
	$t->sendMessage();
}