<?php
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface as IDefaultStockProvider;
use Magento\InventoryCatalog\Model\DefaultStockProvider as DefaultStockProvider;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolver;
use Magento\InventoryIndexer\Model\StockIndexTableNameResolverInterface as IStockIndexTableNameResolver;
/**
 * 2020-11-23
 * @used-by \Justuno\Core\InventoryCatalog\Plugin\Model\ResourceModel\AddStockDataToCollection::aroundExecute()
 * @return IDefaultStockProvider|DefaultStockProvider
 */
function ju_default_stock_provider() {return ju_o(IDefaultStockProvider::class);}

/**
 * 2020-11-23
 * @used-by \Justuno\Core\InventoryCatalog\Plugin\Model\ResourceModel\AddStockDataToCollection::aroundExecute()
 * @return IStockIndexTableNameResolver|StockIndexTableNameResolver
 */
function ju_stock_index_table_name_resolver() {return ju_o(IStockIndexTableNameResolver::class);}