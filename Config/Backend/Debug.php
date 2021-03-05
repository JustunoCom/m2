<?php
namespace Justuno\M2\Config\Backend;
# 2021-03-06
# "Mail database credentials to `travis@justuno.com` on turning the «Provide the developer with the database access» option on":
# https://github.com/justuno-com/m2/issues/35
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Debug extends \Justuno\Core\Config\Backend {
	/**
	 * 2017-08-07
	 * @override
	 * @see \Justuno\Core\Config\Backend::dfSaveAfter()
	 * @used-by \Justuno\Core\Backend::save()
	 */
	final protected function dfSaveAfter() {
		ju_cache_clean();
	}
}