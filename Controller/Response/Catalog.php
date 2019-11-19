<?php
namespace Justuno\M2\Controller\Response;
use Df\Framework\W\Result\Json;
use Justuno\M2\Catalog\Images as cImages;
use Justuno\M2\Catalog\Variants as cVariants;
use Justuno\M2\Filter;
use Magento\Catalog\Model\Category as C;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Visibility as V;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CC;
use Magento\Catalog\Model\ResourceModel\Product\Collection as PC;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Action\Action as _P;
use Magento\Review\Model\Review\Summary as RS;
// 2019-11-17
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Catalog extends _P {
	/**
	 * 2019-11-17
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Json
	 */
	function execute() {/** @var array(string => mixed) $r */
		try {
			if (!df_my_local()
				&& df_request_header('Authorization') !== df_cfg('justuno_settings/options_interface/token_key')
			) {
				df_error('Please provide a valid token key');
			}
			$pc = df_product_c(); /** @var PC $pc */
			$pc->addAttributeToSelect('*');
			/**
			 * 2019-10-30
			 * 1) «if a product has a Status of "Disabled" we'd still want it in the feed,
			 * but we'd want to set the inventoryquantity to -9999»:
			 * https://github.com/justuno-com/m1/issues/4
			 * 2) I do not use
			 * 		$products->setVisibility([V::VISIBILITY_BOTH, V::VISIBILITY_IN_CATALOG, V::VISIBILITY_IN_SEARCH]);
			 * because it filters out disabled products.
			 */
			$pc->addAttributeToFilter('visibility', ['in' => [
				V::VISIBILITY_BOTH, V::VISIBILITY_IN_CATALOG, V::VISIBILITY_IN_SEARCH
			]]);
			$pc->addMediaGalleryData(); // 2019-11-20 https://magento.stackexchange.com/a/228181
			Filter::p($pc);
			$brand = df_cfg('justuno_settings/options_interface/brand_attribute'); /** @var string $brand */
			$r = array_values(array_map(function(P $p) use($brand) { /** @var array(string => mixed) $r */
				$rs = df_new_om(RS::class); /** @var RS $rs */
				$rs->load($p->getId());
				$cc = $p->getCategoryCollection(); /** @var CC $cc */
				$r = [
					'Categories' => array_values(array_map(function(C $c) {return [
						'Description' => $c['description']
						// 2019-10-30
						// «json construct types are not correct for some values»:
						// https://github.com/justuno-com/m1/issues/8
						,'ID' => $c->getId()
						// 2019-10-30
						// «In Categories imageURL is being sent back as a boolean in some cases,
						// it should always be sent back as a string,
						// if there is not url just don't send the property back»:
						// https://github.com/justuno-com/m1/issues/12
						,'ImageURL' => $c->getImageUrl() ?: null
						,'Keywords' => $c['meta_keywords']
						,'Name' => $c->getName()
						,'URL' => $c->getUrl()
					];}, $cc->addAttributeToSelect('*')->addFieldToFilter('level', ['neq' => 1])->getItems()))
					,'CreatedAt' => $p['created_at']
					// 2019-10-30
					// «The parent ID is pulling the sku, it should be pulling the ID like the variant does»:
					// https://github.com/justuno-com/m1/issues/19
					,'ID' => $p->getId()
					/**
					 * 2019-10-30
					 * 1) «MSRP, Price, SalePrice, Variants.MSRP, and Variants.SalePrice all need to be Floats,
					 * or if that is not possible then Ints»: https://github.com/justuno-com/m1/issues/10
					 * 2) «If their isn't an MSRP for some reason just use the salesprice»:
					 * https://github.com/justuno-com/m1/issues/6
					 * 2019-10-31
					 * «The MSRP should pull in this order MSRP > Price > Dynamic Price»:
					 * https://github.com/justuno-com/m1/issues/20
					 */
					,'MSRP' => (float)($p['msrp'] ?: ($p['price'] ?: $p->getPrice()))
					 /**
					  * 2019-10-30
					  * «MSRP, Price, SalePrice, Variants.MSRP, and Variants.SalePrice all need to be Floats,
					  * or if that is not possible then Ints»: https://github.com/justuno-com/m1/issues/10
					  * 2019-10-31
					  * «Price should be Price > Dynamic Price»: https://github.com/justuno-com/m1/issues/21
					  */
					,'Price' => (float)($p['price'] ?: $p->getPrice())
					// 2019-10-30 «ReviewsCount and ReviewSums need to be Ints»: https://github.com/justuno-com/m1/issues/11
					,'ReviewsCount' => (int)$rs->getReviewsCount()
					// 2019-10-30
					// 1) "Add the `ReviewsCount` and `ReviewsRatingSum` values to the `catalog` response":
					// https://github.com/justuno-com/m1/issues/15
					// 2) «ReviewsCount and ReviewSums need to be Ints»: https://github.com/justuno-com/m1/issues/11
					,'ReviewsRatingSum' => (int)$rs->getRatingSummary()
					// 2019-10-30
					// «MSRP, Price, SalePrice, Variants.MSRP, and Variants.SalePrice all need to be Floats,
					// or if that is not possible then Ints»: https://github.com/justuno-com/m1/issues/10
					,'SalePrice' => (float)$p->getPrice()
					,'Title' => $p['name']
					,'UpdatedAt' => $p['updated_at']
					,'URL' => $p->getProductUrl()
					/**
					 * 2019-10-30
					 * «if a product doesn't have parent/child like structure,
					 * I still need at least one variant in the Variants array»:
					 * https://github.com/justuno-com/m1/issues/5
					 */
					,'Variants' => cVariants::p($p)
				] + cImages::p($p);
				if ('configurable' === $p->getTypeId()) {
					$ct = $p->getTypeInstance(); /** @var Configurable $ct */
					$opts = array_column($ct->getConfigurableAttributesAsArray($p), 'attribute_code', 'id');
					/**
					 * 2019-10-30
					 * «within the ProductResponse and the Variants OptionType is being sent back as OptionType90, 91, etc...
					 * We need these sent back starting at OptionType1, OptionType2»:
					 * https://github.com/justuno-com/m1/issues/14
					 */
					foreach (array_values($opts) as $id => $code) {$id++; /** @var int $id */ /** @var string $code */
						$r["OptionType$id"] = $code;
					}
				}
				/**
				 * 2019-11-01
				 * If $brand is null, then @uses Mage_Catalog_Model_Product::getAttributeText() fails.
				 * https://www.upwork.com/messages/rooms/room_e6b2d182b68bdb5e9bf343521534b1b6/story_4e29dacff68f2d918eff2f28bb3d256c
				 */
				return $r + ['BrandId' => $brand, 'BrandName' => !$brand ? null : ($p->getAttributeText($brand) ?: null)];
			}, $pc->getItems()));
		}
		catch (\Exception $e) {
			$r = ['message' => $e->getMessage(), 'response' => null];
		}
		return Json::i($r);
	}
}