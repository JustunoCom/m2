<?php
use Magento\Eav\Model\Entity\AbstractEntity as Entity;
use Magento\Framework\DB\Select;

/**
 * 2016-12-01
 * 1) $cols could be:
 * 1.1) a string to fetch a single column;
 * 1.2) an array to fetch multiple columns.
 * @see \Zend_Db_Select::_tableCols()
 *		if (!is_array($cols)) {
 *			$cols = array($cols);
 *		}
 * https://github.com/zendframework/zf1/blob/release-1.12.16/library/Zend/Db/Select.php#L929-L931
 * 2) The function always returns @see Select
 * I added @see \Zend_Db_Select to the PHPDoc return type declaration just for my IDE convenience.
 * 2020-08-22 "Port the `ju_db_from` function" https://github.com/justuno-com/core/issues/267
 * @used-by ju_fetch()
 * @used-by ju_fetch_col()
 * @used-by ju_fetch_one()
 * @used-by \Justuno\M2\Store::v()
 * @param string|Entity|array(string => string) $t
 * @param string|string[] $cols [optional]
 * @param string|null $schema [optional]
 * @return Select|\Zend_Db_Select
 */
function ju_db_from($t, $cols = '*', $schema = null) {return ju_select()->from(
	$t instanceof Entity ? $t->getEntityTable() : (is_array($t) ? $t : ju_table($t)), $cols, $schema
);}

/**
 * 2016-12-23 http://stackoverflow.com/a/10414925
 * 2020-08-14 "Port the `df_db_version` function" https://github.com/justuno-com/core/issues/190
 * @used-by ju_sentry_m()
 * @see \Magento\Backup\Model\ResourceModel\Helper::getHeader()
 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/Backup/Model/ResourceModel/Helper.php#L178
 * @return string
 */
function ju_db_version() {return jucf(function() {return ju_conn()->fetchRow("SHOW VARIABLES LIKE 'version'")['Value'];});}