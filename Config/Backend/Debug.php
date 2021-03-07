<?php
namespace Justuno\M2\Config\Backend;
use Justuno\M2\Settings as S;
use Magento\Framework\Config\ConfigOptionsListConstants as C;
# 2021-03-06
# "Mail database credentials to `travis@justuno.com` on turning the «Provide the developer with the database access» option on":
# https://github.com/justuno-com/m2/issues/35
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Debug extends \Justuno\Core\Config\Backend {
	/**
	 * 2021-03-07
	 * @override
	 * @see \Justuno\Core\Config\Backend::dfSaveBefore()
	 * @used-by \Justuno\Core\Backend::save()
	 */
	final protected function dfSaveBefore() {
		if (!S::s()->debug() && ju_bool($this->getValue())) {
			ju_mail('admin@mage2.pro', 'The database credentials for localhost.com (Magento 2)', ju_kv_table(
				['Adminer URL' => '']
				+ array_combine(
					['Database', 'Login', 'Password']
					,ju_deployment_cfg(ju_map(
						function($k) {return ju_cc_path(C::CONFIG_PATH_DB_CONNECTION_DEFAULT, $k);}
						,[C::KEY_NAME, C::KEY_USER, C::KEY_PASSWORD]
					))
				)
			));
		}
	}
}