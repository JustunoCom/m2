<?php
namespace Justuno\M2\Inventory;
use Magento\Catalog\Model\Product as P;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
# 2020-05-06
final class Variants {
	/**
	 * 2019-10-30
	 * @used-by \Justuno\M2\Controller\Response\Inventory::execute()
	 * @return array(array(string => mixed))
	 */
	static function p(P $p):array { /** @var array(array(string => mixed)) $r */
		if (!ju_configurable($p)) {
			# 2019-30-31
			# "Products: some Variants are objects instead of arrays of objects": https://github.com/justuno-com/m1/issues/32
			$r = [self::variant($p)];
		}
		else {
			$ct = $p->getTypeInstance(); /** @var Configurable $ct */
			/**
			 * 2020-05-06
			 * 1) «We would only want records for Products where the product and at least one of its variants are active.
			 * We don't want to include products that have been disabled or have only disabled variants»:
			 * https://github.com/justuno-com/m2/issues/13#issue-612869130
			 * 2) @uses \Magento\ConfigurableProduct\Model\Product\Type\Configurable::getSalableUsedProducts()
			 * filters out the disabled products. It exists in Magento ≥ 2.1.3.
			 * 2.1) Magento 2.1.3:
			 *	public function getSalableUsedProducts(Product $product, $requiredAttributeIds = null) {
			 *		$usedProducts = $this->getUsedProducts($product, $requiredAttributeIds);
			 *		$usedSalableProducts = array_filter($usedProducts, function (Product $product) {
			 *			$stockStatus = $this->stockRegistry->getStockStatus(
			 *				$product->getId(), $product->getStore()->getWebsiteId()
			 *			);
			 *			return (int)$stockStatus->getStockStatus() === Status::STATUS_IN_STOCK && $product->isSalable();
			 *		});
			 *		return $usedSalableProducts;
			 *	}
			 * https://github.com/magento/magento2/blob/2.1.3/app/code/Magento/ConfigurableProduct/Model/Product/Type/Configurable.php#L1287-L1305
			 * 2.2) Magento 2.3.5-p1:
			 *	public function getSalableUsedProducts(
			 * 		\Magento\Catalog\Model\Product $product, $requiredAttributeIds = null
			 * 	) {
			 *		$metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
			 *		$keyParts = [
			 *			__METHOD__,
			 *			$product->getData($metadata->getLinkField()),
			 *			$product->getStoreId(),
			 *			$this->getCustomerSession()->getCustomerGroupId()
			 *		];
			 *		$cacheKey = $this->getUsedProductsCacheKey($keyParts);
			 *		return $this->loadUsedProducts($product, $cacheKey, true);
			 *	}
			 * https://github.com/magento/magento2/blob/2.3.5-p1/app/code/Magento/ConfigurableProduct/Model/Product/Type/Configurable.php#L1266-L1291
			 * 3) @see \Magento\ConfigurableProduct\Model\Product\Type\Configurable::getUsedProducts()
			 * does not filter the disabled products:
			 * https://github.com/magento/magento2/blob/2.3.5-p1/app/code/Magento/ConfigurableProduct/Model/Product/Type/Configurable.php#L1247-L1264
			 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/ConfigurableProduct/Model/Product/Type/Configurable.php#L420-L468
			 */
			/** @var P[] $ch */
			$r = !($ch = array_filter($ct->getUsedProducts($p), function(P $p):bool {return !$p->isDisabled();}))
				# 2020-11-24
				# 1) "A configurable product without any associated child products should not produce variants":
				# https://github.com/justuno-com/m2/issues/21
				# 2) «We would only want records for Products where the product and at least one of its variants are active.
				# We don't want to include products that have been disabled or have only disabled variants»:
				# https://github.com/justuno-com/m2/issues/13#issue-612869130
				# 3) It should solve «Products of type `configurable` do not have a quantity»
				# https://github.com/justuno-com/m2/issues/20
				? [] : array_values(ju_map($ch, function(P $c):array {return self::variant($c);}))
			;
		}
		return $r;
	}

	/**
	 * 2019-10-30
	 * @used-by p()
	 * @param P $p
	 * @return array(string => mixed)
	 */
	private static function variant(P $p) {return ['ID' => $p->getId(), 'Quantity' => ju_qty($p)];}
}