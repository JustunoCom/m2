<?php
use Magento\Catalog\Model\Product as P;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite as StockIdForWebsite;
use Magento\InventorySalesApi\Model\GetAssignedStockIdForWebsiteInterface as IStockIdForWebsite;
use Magento\Store\Model\Store;
use Magento\Store\Model\Website as W;
/**
 * 2019-11-22
 * 2020-08-23 "Port the `df_msi` function" https://github.com/justuno-com/core/issues/276
 * @used-by ju_pt_has_qty()
 * @used-by ju_qty()
 * @return bool
 */
function ju_msi() {return jucf(function() {return ju_module_enabled('Magento_Inventory');});}

/**
 * 2019-11-22
 * 2020-08-23 "Port the `df_msi_stock_ids` function" https://github.com/justuno-com/core/issues/279
 * @used-by ju_qty()
 * @uses ju_msi_website2stockId()
 * @param P $p
 * @return int[]
 */
function ju_msi_stock_ids(P $p) {return array_filter(array_unique(array_map('ju_msi_website2stockId', $p->getWebsiteIds())));}

/**
 * 2019-11-22
 * 1) It returns null if the website is not linked to a stock.
 * 2) I use the @uses dfcf() caching because
 * @uses \Magento\InventorySales\Model\ResourceModel\GetAssignedStockIdForWebsite::execute()
 * makes a direct query to the database.
 * 3) The $v argument could be one of:
 * *) a website: W
 * *) a store: Store
 * *) a website's ID: int
 * *) a website's code: string
 * *) null or absert: the current website
 * *) true: the default website
 * 2020-08-23 "Port the `df_msi_website2stockId` function" https://github.com/justuno-com/core/issues/284
 * @used-by ju_msi_stock_ids()
 * @param W|Store|int|string|null|bool $v [optional]
 * @return int|null
 * @throws Exception
 * @throws NSE
 */
function ju_msi_website2stockId($v = null) {return jucf(function($c) {
	$i = ju_o(StockIdForWebsite::class); /** @var IStockIdForWebsite|StockIdForWebsite $i */
	return $i->execute($c);
}, [ju_website_code($v)]);}