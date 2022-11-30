<?php
namespace Justuno\M2\Controller\Response;
use Justuno\Core\Framework\W\Result\Json;
use Justuno\M2\Catalog\Diagnostic;
use Justuno\M2\Catalog\Images as cImages;
use Justuno\M2\Catalog\Variants as cVariants;
use Justuno\M2\Filter;
use Justuno\M2\Response as R;
use Justuno\M2\Settings as S;
use Justuno\M2\Store;
use Magento\Catalog\Model\Category as C;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Visibility as V;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CC;
use Magento\Catalog\Model\ResourceModel\Product\Collection as PC;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Action\Action as _P;
use Magento\Review\Model\Review\Summary as RS;
/** 2019-11-17 @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Catalog extends _P {
	/**
	 * 2019-11-17
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 */
	function execute():Json {return R::p(function() {
		# 2020-11-23
		# If the flat mode is enabled, then the products collection misses disabled products,
		# because the `catalog_product_flat_<store>` table does not contain disabled products at least in Magento 2.4.0.
		# It is wrong because disabled products should be in the feed: https://github.com/justuno-com/m2/issues/19
		# 2020-11-24
		# 1) "The `jumagext/response/catalog` response does not contain disabled products
		# if the «Use Flat Catalog Product» option is enabled": https://github.com/justuno-com/m2/issues/23
		# 2) "Add an ability to temporary disable the flat mode for products": https://github.com/mage2pro/core/issues/149
		ju_pc_disable_flat();
		$pc = ju_pc(Store::v()); /** @var PC $pc */
		$pc->addAttributeToSelect('*');
		# 2019-10-30
		# 1) «if a product has a Status of "Disabled" we'd still want it in the feed,
		# but we'd want to set the inventoryquantity to -9999»:
		# https://github.com/justuno-com/m1/issues/4
		# 2) I do not use
		# 		$pc->setVisibility([V::VISIBILITY_BOTH, V::VISIBILITY_IN_CATALOG, V::VISIBILITY_IN_SEARCH]);
		# because it filters out the disabled products.
		$pc->addAttributeToFilter('visibility', ['in' => [
			V::VISIBILITY_BOTH, V::VISIBILITY_IN_CATALOG, V::VISIBILITY_IN_SEARCH
		]]);
		/**
		 * 2019-11-22
		 * @uses \Magento\Catalog\Model\ResourceModel\Product\Collection::addMediaGalleryData() loads the collection,
		 * so we should apply filters before it, not after.
		 * «Filters do not work for `catalog`»: https://github.com/justuno-com/m2/issues/5
		 */
		Filter::p($pc);
		# 2020-11-23
		# 1) "The `jumagext/response/catalog` response does not contain disabled products":
		# https://github.com/justuno-com/m2/issues/19
		# 2) @todo It solves the problem only if the flat mode is disabled.
		ju_pc_preserve_absent($pc);
		$pc->addMediaGalleryData(); # 2019-11-20 https://magento.stackexchange.com/a/228181
		$brand = S::s()->brand_attribute(); /** @var string $brand */
		ju_sentry_extra($this, ['Products count' => count($pc), 'Products SQL' => (string)$pc->getSelect()->assemble()]);
		# 2021-02-25
		# "Provide a diagnostic message if the requested product is not eligible for the feed":
		# https://github.com/justuno-com/m2/issues/32
		if (ju_request('id') && !count($pc)) {
			Diagnostic::p();
		}
		return array_values(ju_map($pc, function(P $p) use($brand) { /** @var array(string => mixed) $r */
			$rs = ju_review_summary($p); /** @var RS $rs */
			$cc = $p->getCategoryCollection(); /** @var CC $cc */
			$price = self::price($p); /** @var float $price */
			$r = [
				'Categories' => array_values(array_map(function(C $c) {return [
					# 2021-02-05
					# «remove the description and keywords parameters from the categories object»:
					# https://github.com/justuno-com/m2/issues/25
					# 2019-10-30
					# «json construct types are not correct for some values»:
					# https://github.com/justuno-com/m1/issues/8
					'ID' => $c->getId()
					# 2019-10-30
					# «In Categories imageURL is being sent back as a boolean in some cases,
					# it should always be sent back as a string,
					# if there is not url just don't send the property back»:
					# https://github.com/justuno-com/m1/issues/12
					,'ImageURL' => $c->getImageUrl() ?: null
					# 2021-02-05
					# «remove the description and keywords parameters from the categories object»:
					# https://github.com/justuno-com/m2/issues/25
					,'Name' => $c->getName()
					,'URL' => $c->getUrl()
				];}, $cc->addAttributeToSelect('*')->addFieldToFilter('level', ['neq' => 1])->getItems()))
				,'CreatedAt' => $p['created_at']
				# 2019-10-30
				# «The parent ID is pulling the sku, it should be pulling the ID like the variant does»:
				# https://github.com/justuno-com/m1/issues/19
				,'ID' => $p->getId()
				# 2019-10-30
				# 1) «MSRP, Price, SalePrice, Variants.MSRP, and Variants.SalePrice all need to be Floats,
				# or if that is not possible then Ints»: https://github.com/justuno-com/m1/issues/10
				# 2) «If their isn't an MSRP for some reason just use the salesprice»:
				# https://github.com/justuno-com/m1/issues/6
				# 2019-10-31
				# «The MSRP should pull in this order MSRP > Price > Dynamic Price»: https://github.com/justuno-com/m1/issues/20
				,'MSRP' => (float)($p['msrp'] ?: ($p['price'] ?: $price))
				# 2019-10-30
				# «MSRP, Price, SalePrice, Variants.MSRP, and Variants.SalePrice all need to be Floats,
				# or if that is not possible then Ints»: https://github.com/justuno-com/m1/issues/10
				# 2019-10-31 «Price should be Price > Dynamic Price»: https://github.com/justuno-com/m1/issues/21
				,'Price' => (float)($p['price'] ?: $price)
				# 2019-10-30 «ReviewsCount and ReviewSums need to be Ints»: https://github.com/justuno-com/m1/issues/11
				,'ReviewsCount' => (int)$rs->getReviewsCount()
				# 2019-10-30
				# 1) "Add the `ReviewsCount` and `ReviewsRatingSum` values to the `catalog` response":
				# https://github.com/justuno-com/m1/issues/15
				# 2) «ReviewsCount and ReviewSums need to be Ints»: https://github.com/justuno-com/m1/issues/11
				,'ReviewsRatingSum' => (int)$rs->getRatingSummary()
				# 2019-10-30
				# «MSRP, Price, SalePrice, Variants.MSRP, and Variants.SalePrice all need to be Floats,
				# or if that is not possible then Ints»: https://github.com/justuno-com/m1/issues/10
				,'SalePrice' => $price
				,'Title' => $p['name']
				,'UpdatedAt' => $p['updated_at']
				,'URL' => $p->getProductUrl()
				# 2019-10-30
				# «if a product doesn't have parent/child like structure,
				# I still need at least one variant in the Variants array»: https://github.com/justuno-com/m1/issues/5
				,'Variants' => cVariants::p($p)
			] + cImages::p($p);
			if (ju_configurable($p)) {
				$ct = $p->getTypeInstance(); /** @var Configurable $ct */
				# 2021-02-05
				# «The OptionType1 and 2 here seem to be internal identifiers rather than whats displayed on the site.
				# I would want Color and Size like it's displayed on the actual product page of their site»
				$opts = array_column($ct->getConfigurableAttributesAsArray($p), 'store_label');
				# 2019-10-30
				# «within the ProductResponse and the Variants OptionType is being sent back as OptionType90, 91, etc...
				# We need these sent back starting at OptionType1, OptionType2»: https://github.com/justuno-com/m1/issues/14
				foreach ($opts as $id => $code) {$id++; /** @var int $id */ /** @var string $code */
					$r["OptionType$id"] = $code;
				}
			}
			/**
			 * 2019-11-01
			 * If $brand is null, then @uses \Magento\Catalog\Model\Product::getAttributeText() fails.
			 * https://www.upwork.com/messages/rooms/room_e6b2d182b68bdb5e9bf343521534b1b6/story_4e29dacff68f2d918eff2f28bb3d256c
			 */
			return $r + ['BrandId' => $brand, 'BrandName' => !$brand ? null : ($p->getAttributeText($brand) ?: null)];
		}));
	});}

	/**
	 * 2021-02-05
	 * @used-by execute()
	 * @param P $p
	 * @return float
	 */
	private static function price(P $p) {
		# 2021-03-24 "Replace `getPrice` with `getFinalPrice`": https://github.com/justuno-com/m2/issues/36
		$r = $p->getFinalPrice(); /** @var float $r */
		# 2021-02-05
		# 1) «the parent product has 0 for pricing or MSRP and price are correct and then saleprice is 0 which isn't correct»:
		# https://github.com/justuno-com/m2/issues/29
		# 2) https://webkul.com/blog/get-price-range-configurable-product-magento-2
		if (!$r && ju_configurable($p)) {
			/**
			 * 2021-02-05
			 * @uses \Magento\ConfigurableProduct\Pricing\Price\ConfigurableRegularPrice::getMinRegularAmount()
			 * always returns an object even in the configurable product does have children.
			 */
			$r = $p->getPriceInfo()->getPrice('regular_price')->getMinRegularAmount()->getValue();
		}
		return (float)$r;
	}
}