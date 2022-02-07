<?php
use Magento\Framework\App\ResourceConnection as RC;
use Magento\Framework\DB\Adapter\AdapterInterface as IAdapter;
use Magento\Framework\DB\Adapter\Pdo\Mysql;
use Magento\Framework\DB\Ddl\Trigger;
use Magento\Framework\DB\Select;

/**
 * 2020-08-14 "Port the `df_conn` function" https://github.com/justuno-com/core/issues/191
 * @used-by ju_db_version()
 * @used-by ju_fetch()
 * @used-by ju_fetch_col()
 * @used-by ju_fetch_one()
 * @used-by ju_select()
 * @used-by ju_table_exists()
 * @used-by \Justuno\M2\Store::v()
 * @used-by \Justuno\M2\Setup\UpgradeSchema::tr()
 * @return Mysql|IAdapter
 */
function ju_conn() {return ju_db_resource()->getConnection();}

/**
 * 2015-09-29
 * 2020-08-14 "Port the `df_db_resource` function" https://github.com/justuno-com/core/issues/192
 * @used-by ju_conn()
 * @used-by ju_table()
 * @return RC
 */
function ju_db_resource() {return ju_o(RC::class);}

/**
 * 2015-09-29
 * 2016-12-01
 * The function always returns @see Select
 * I added @see \Zend_Db_Select to the PHPDoc return type declaration just for my IDE convenience.
 * 2020-08-23 "Port the `df_select` function" https://github.com/justuno-com/core/issues/269
 * @used-by ju_db_from()
 * @return Select|\Zend_Db_Select
 */
function ju_select() {return ju_conn()->select();}

/**
 * 2019-11-22
 * 2020-08-21 "Port the `df_trigger` function" https://github.com/justuno-com/core/issues/231
 * @used-by \Justuno\M2\Setup\UpgradeSchema::tr()
 * @return Trigger
 */
function ju_trigger() {return ju_new_om(Trigger::class);}