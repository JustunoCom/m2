<?php
namespace Justuno\M2\Setup;
use Magento\Framework\DB\Ddl\Trigger as T;
# 2019-11-22
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class UpgradeSchema extends \Justuno\Core\Framework\Upgrade\Schema {
	/**
	 * 2019-11-22
	 * @override
	 * @see \Justuno\Core\Framework\Upgrade::_process()
	 * @used-by \Justuno\Core\Framework\Upgrade::process()
	 */
	final protected function _process():void {
		$t_catalog_product_entity = ju_table('catalog_product_entity'); /** @var string $t_catalog_product_entity */
		$t_catalog_product_super_link = ju_table('catalog_product_super_link'); /** @var string $t_catalog_product_super_link */
		if ($this->v('1.6.3')) {
			# 2021-03-05
			# «trigger for updating catalog_product_entity running very slow for large catalog»:
			# https://github.com/justuno-com/m1/issues/52
			$this->tr('cataloginventory_stock_status', "
				UPDATE $t_catalog_product_entity
					SET updated_at = CURRENT_TIMESTAMP()
					WHERE entity_id = NEW.product_id
				;
				UPDATE $t_catalog_product_super_link l
					INNER JOIN $t_catalog_product_entity e ON (e.entity_id = l.parent_id)
					SET e.updated_at = CURRENT_TIMESTAMP()
					WHERE l.product_id = NEW.product_id
				;
			");
		}
		if ($this->v('1.6.5')) {
			# 2019-11-22
			# I splitted the trigger for `inventory_reservation` into 2 parts to overcome the issue:
			# «You can't specify target table '...' for update in FROM clause»
			# https://stackoverflow.com/questions/45494
			# 2021-03-24
			# I have merged 2 `UPDATE` statements into the same trigger to overcome the issue:
			# «This version of MariaDB doesn't yet support
			# 'multiple triggers with the same action time and event for one table»:
			# https://github.com/justuno-com/m2/issues/15#issuecomment-805550630
			$this->tr('inventory_reservation', "
				UPDATE $t_catalog_product_entity
					SET updated_at = CURRENT_TIMESTAMP()
					WHERE sku = NEW.sku
				;
				UPDATE $t_catalog_product_entity e1
					INNER JOIN $t_catalog_product_super_link s
						ON s.product_id = e1.entity_id AND NEW.sku = e1.sku
					INNER JOIN $t_catalog_product_entity e2
						ON e2.entity_id = s.parent_id
					SET e2.updated_at = CURRENT_TIMESTAMP()
				;				
			");
			$this->tr('inventory_reservation', '', 2);
		}
	}

	/**
	 * 2019-11-22
	 * @used-by self::_process()
	 */
	private function tr(string $t, string $sql = '', string $suffix = ''):void {
		# 2019-11-30 "The `inventory_reservation` table is absent in Magento < 2.3": https://github.com/justuno-com/m2/issues/6
		if (ju_table_exists($t)) {
			foreach ([T::EVENT_INSERT, T::EVENT_UPDATE] as $e) {
				# 2020-08-27
				# «This version of MariaDB doesn't yet support
				# 'multiple triggers with the same action time and event for one table»:
				# https://github.com/justuno-com/m2/issues/15
				ju_conn()->dropTrigger($name = ju_ccc('__', 'justuno', $t, strtolower($e), $suffix)); /** @var string $name */
				if ($sql) {
					ju_conn()->createTrigger(ju_trigger()
						->addStatement($sql)
						->setEvent($e)
						->setName($name)
						->setTable(ju_table($t))
						->setTime(T::TIME_AFTER)
					);
				}
			}
		}
	}
}