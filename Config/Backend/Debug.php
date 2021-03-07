<?php
namespace Justuno\M2\Config\Backend;
use Magento\Framework\Config\ConfigOptionsListConstants as C;
# 2021-03-06
# "Mail database credentials to `travis@justuno.com` on turning the «Provide the developer with the database access» option on":
# https://github.com/justuno-com/m2/issues/35
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Debug extends \Justuno\Core\Config\Backend {
	/**
	 * 2021-03-06
	 * @override
	 * @see \Justuno\Core\Config\Backend::dfSaveAfter()
	 * @used-by \Justuno\Core\Backend::save()
	 */
	final protected function dfSaveAfter() {
		if (ju_bool($this->getValue())) {
			ju_sentry_extra($this, array_combine(
				['DB', 'DB Login', 'DB Password']
				,ju_deployment_cfg(ju_map(
					function($k) {return ju_cc_path(C::CONFIG_PATH_DB_CONNECTION_DEFAULT, $k);}
					,[C::KEY_NAME, C::KEY_USER, C::KEY_PASSWORD]
				))
			));
		}
	}
}