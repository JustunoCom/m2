<?php
namespace Justuno\M2\Plugin\Framework\Session;
use Magento\Framework\Session\SessionStartChecker as Sb;
# 2021-02-23 "Implement a database diagnostic tool": https://github.com/justuno-com/core/issues/34
final class SessionStartChecker {
	/**
	 * 2021-02-23
	 * @see \Magento\Framework\Session\SessionStartChecker::check()
	 * @param Sb $sb
	 * @param bool $r
	 * @return bool
	 */
	function afterCheck(Sb $sb, $r) {return $r && !ju_rp_has('justuno/db');}
}