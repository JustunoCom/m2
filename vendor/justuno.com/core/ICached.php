<?php
namespace Justuno\Core;
# 2017-08-30
interface ICached {
	/**
	 * 2017-08-30
	 * @used-by \Justuno\Core\RAM::set()
	 * @return string[]
	 */
	function tags();
}