<?php
namespace Justuno\M2\Plugin\Framework\Session;
use Magento\Framework\Session\SessionStartChecker as Sb;
# 2021-02-23 "Implement a database diagnostic tool": https://github.com/justuno-com/core/issues/34
final class SessionStartChecker {
	/**
	 * 2021-02-23
	 * @see \Magento\Framework\Session\SessionStartChecker::check()
	 */
	function afterCheck(Sb $sb, bool $r):bool {return $r && !ju_rp_has('justuno/db');}
}