<?php
namespace Justuno\M2\Catalog;
use Magento\Catalog\Api\Data\ProductInterface as IP;
use Magento\Catalog\Model\Product as P;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
// 2019-10-30
final class Variants {
	/**
	 * 2019-10-30
	 * @used-by \Justuno_Jumagext_ResponseController::catalogAction
	 * @param P $p
	 * @return array(array(string => mixed))
	 */
	static function p(P $p) { /** @var array(array(string => mixed)) $r */
		if ('configurable' !== $p->getTypeId()) {
			// 2019-30-31
			// "Products: some Variants are objects instead of arrays of objects":
			// https://github.com/justuno-com/m1/issues/32
			$r = [self::variant($p)];
		}
		else {
			$ct = $p->getTypeInstance(); /** @var Configurable $ct */
			// 2019-30-31
			// "A configurable product without any associated child products does not produce variants":
			// https://github.com/justuno-com/m1/issues/26
			if (!($children = $ct->getUsedProducts($p))) { /** @var P $children */
				// 2019-30-31
				// "Products: some Variants are objects instead of arrays of objects":
				// https://github.com/justuno-com/m1/issues/32
				$r = [self::variant($p)];
			}
			else {
				$opts = array_column($ct->getConfigurableAttributesAsArray($p), 'attribute_code', 'id');
				$r = array_values(array_map(function(P $c) use($opts, $p) {return
					self::variant($c, $p, $opts)
				;}, $children));
			}
		}
		return $r;
	}

	/**
	 * 2019-10-30
	 * @param P $p
	 * @param P|IP|null $parent [optional]
	 * @param array(int => string) $opts [optional]
	 * @return array(string => mixed)
	 */
	private static function variant(P $p, P $parent = null, $opts = []) {
		$r = [
			'ID' => $p->getId()
			/**
			 * 2019-10-30
			 * «if a product has a Status of "Disabled" we'd still want it in the feed,
			 * but we'd want to set the inventoryquantity to -9999»:
			 * https://github.com/justuno-com/m1/issues/4
			 * 2019-11-06
			 * «if I set the parent product to disabled,
			 * all the variants that are not disabled still show their entered inventory»:
			 * https://github.com/justuno-com/m1/issues/35
			 */
			,'InventoryQuantity' => $p->isDisabled() || ($parent && $parent->isDisabled()) ? -9999 : df_qty($p)
			/**
			 * 2019-10-30
			 * 1) «MSRP, Price, SalePrice, Variants.MSRP, and Variants.SalePrice all need to be Floats,
			 * or if that is not possible then Ints»: https://github.com/justuno-com/m1/issues/10
			 * 2) «MSRP was null for some variants but the MSRP wasn't null for the parent»:
			 * https://github.com/justuno-com/m1/issues/7
			 * 3) «If their isn't an MSRP for some reason just use the salesprice»:
			 * https://github.com/justuno-com/m1/issues/6
			 * 2019-10-31
			 * «For variant pricing,
			 * i would want the flow to be the same as the MSRP and SalePrice from the parent above
			 * but using the variant's pricing of course»: https://github.com/justuno-com/m1/issues/25
			 */
			,'MSRP' => (float)($p['msrp'] ?: ($p['price'] ?: $p->getPrice()))
			// 2019-10-30
			// «MSRP, Price, SalePrice, Variants.MSRP, and Variants.SalePrice all need to be Floats,
			// or if that is not possible then Ints»: https://github.com/justuno-com/m1/issues/10
			,'SalePrice' => (float)$p->getPrice()
			,'SKU' => $p->getSku()
			,'Title' => $p->getName()
		];
		/**
		 * 2019-10-30
		 * «within the ProductResponse and the Variants OptionType is being sent back as OptionType90, 91, etc...
		 * We need these sent back starting at OptionType1, OptionType2»:
		 * https://github.com/justuno-com/m1/issues/14
		 */
		foreach (array_values($opts) as $id => $code) {$id++;
			$r["Option$id"] = $p->getAttributeText($code);
		}
		return $r;
	}
}