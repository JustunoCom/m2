<?php
namespace Justuno\M2\Setup;
use Magento\Framework\DB\Ddl\Trigger as T;
# 2019-11-22
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class UpgradeSchema extends \Df\Framework\Upgrade\Schema {
	/**
	 * 2019-11-22
	 * @override
	 * @see \Df\Framework\Upgrade::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 */
	final protected function _process() {
		if ($this->v('1.1.7')) {
			$this->tr('cataloginventory_stock_status', "
				UPDATE catalog_product_entity
				SET updated_at = CURRENT_TIMESTAMP()
				WHERE
					entity_id = NEW.product_id
					OR entity_id IN (SELECT parent_id FROM catalog_product_super_link WHERE product_id = NEW.product_id)	
			");
			$this->tr('inventory_reservation', "
				UPDATE catalog_product_entity
				SET updated_at = CURRENT_TIMESTAMP()
				WHERE sku = NEW.sku	
			");
			# 2019-11-22
			# I splitted the trigger for `inventory_reservation` into 2 parts to overcome the issue:
			# «You can't specify target table '...' for update in FROM clause»
			# https://stackoverflow.com/questions/45494
			$this->tr('inventory_reservation', "
				UPDATE catalog_product_entity e1
				INNER JOIN catalog_product_super_link s
					ON s.product_id = e1.entity_id AND NEW.sku = e1.sku
				INNER JOIN catalog_product_entity e2
					ON e2.entity_id = s.parent_id
				SET e2.updated_at = CURRENT_TIMESTAMP()
			", 2);
		}
	}

	/**
	 * 2019-11-22
	 * @used-by _process()
	 * @param string $t
	 * @param string $sql
	 * @param string|int $suffix [optional]
	 */
	private function tr($t, $sql, $suffix = '') {
		# 2019-11-30
		# "The `inventory_reservation` table is absent in Magento < 2.3":
		# https://github.com/justuno-com/m2/issues/6
		if (df_table_exists($t)) {
			foreach ([T::EVENT_INSERT, T::EVENT_UPDATE] as $e) {
				df_conn()->createTrigger(df_trigger()
					->addStatement($sql)
					->setEvent($e)
					->setName(df_ccc('__', 'justuno', $t, strtolower($e), $suffix))
					->setTable(df_table($t))
					->setTime(T::TIME_AFTER)
				);
			}
		}
	}
}