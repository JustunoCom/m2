<?php
namespace Justuno\M2\Model\Config;
class Mylist implements \Magento\Framework\Option\ArrayInterface {
	/**
	 * {@inheritdoc}
	 *
	 * @codeCoverageIgnore
	 */
	function toOptionArray()
	{

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource   = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName  = $resource->getTableName('eav_attribute');
		$Query      = "Select * FROM " . $tableName. " where entity_type_id=4";
		$result     = $connection->fetchAll($Query);
		$attributes = array();
		$attributes[] = ['value' => '', 'label' => __('Please Select')];

		foreach ($result as $a) {
			/**
			 * 2019-10-245 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			 * "The `manufaturer` attribute is absent in the «Choose Brand Attribute» backend dropdown":
			 * https://github.com/justuno-com/m2/issues/3
			 */
			if (!$a['is_user_defined'] || 'manufacturer' === $a['attribute_code']) {
				$attributes[] = ['value' => $a['attribute_code'], 'label' => __($a['frontend_label'])];
			}
		}
		return $attributes;
	}
}
