<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Justuno\Jumagext\Model\Config;

class Mylist implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource   = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName  = $resource->getTableName('eav_attribute');
        $Query      = "Select * FROM " . $tableName. " where entity_type_id=4";
        $result     = $connection->fetchAll($Query);
        $attributes = array();
        $attributes[] = ['value' => '', 'label' => __('Please Select')];

        foreach($result as $data){
            if($data['is_user_defined'] == 0){
                $attributes[] = ['value' => $data['attribute_code'], 'label' => __($data['frontend_label'])];
            }
        }
        return $attributes;
    }
}
