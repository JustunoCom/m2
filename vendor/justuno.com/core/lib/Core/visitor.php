<?php
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress as RA;

/**
 * 2020-08-14 "Port the `df_visitor_ip` function" https://github.com/justuno-com/core/issues/183
 * @used-by ju_sentry_m()
 * @return string
 */
function ju_visitor_ip() {
	/** @var RA $a */ $a = ju_o(RA::class); return ju_my_local() ? '92.243.166.8' : $a->getRemoteAddress();
}