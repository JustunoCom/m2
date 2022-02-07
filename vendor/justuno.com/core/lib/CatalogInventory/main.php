<?php
use Magento\Catalog\Model\Product as P;
use Magento\CatalogInventory\Api\StockRegistryInterface as IStockRegistry;
use Magento\CatalogInventory\Model\StockRegistry;
use Magento\InventorySales\Model\GetProductSalableQty as Qty;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface as IQty;

/**
 * 2019-11-18 It returns a float value, not an integer one.
 * 2020-08-23 "Port the `df_qty` function" https://github.com/justuno-com/core/issues/274
 * @used-by \Justuno\M2\Catalog\Variants::variant()
 * @used-by \Justuno\M2\Inventory\Variants::variant()
 * @param P|int $p
 * @return float
 */
function ju_qty($p) {
	ju_assert_qty_supported($p);
	# 2019-11-21 https://devdocs.magento.com/guides/v2.3/inventory/reservations.html#checkout-services
	if (!ju_msi()) {
		$r = ju_stock_r()->getStockItem(ju_product_id($p))->getQty();
	}
	else {
		$i = ju_o(IQty::class); /** @var IQty|Qty $i */
		$sku = $p->getSku(); /** @var string $sku */
		$r = array_sum(array_map(function($sid) use($i, $sku) {return $i->execute($sku, $sid);}, ju_msi_stock_ids($p)));
	}
	return $r;
}

/**
 * 2018-06-04
 * 2020-08-23 "Port the `df_stock_r` function" https://github.com/justuno-com/core/issues/277
 * @used-by ju_qty()
 * @return IStockRegistry|StockRegistry
 */
function ju_stock_r() {return ju_o(IStockRegistry::class);}