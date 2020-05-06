<?php
namespace Justuno\M2\Controller\Response;
use Df\Framework\W\Result\Json;
use Justuno\M2\Inventory\Variants as cVariants;
use Justuno\M2\Response as R;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\Product\Visibility as V;
use Magento\Catalog\Model\ResourceModel\Product\Collection as PC;
use Magento\Framework\App\Action\Action as _P;
/**
 * 2020-05-06
 * "Implement an endpoint to return product quantities": https://github.com/justuno-com/m2/issues/13
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 */
class Inventory extends _P {
	/**
	 * 2020-05-06
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Json
	 */
	function execute() {return R::p(function() {
		$pc = df_product_c(); /** @var PC $pc */
		$pc->addAttributeToSelect('*');
		/**
		 * 2019-10-30
		 * 1) «We don't want to include products that have been disabled or have only disabled variants»:
		 * https://github.com/justuno-com/m2/issues/13#issue-612869130
		 * 2) @uses \Magento\Catalog\Model\ResourceModel\Product\Collection::setVisibility()
		 * filters out the disabled products.
		 */
		$pc->setVisibility([V::VISIBILITY_BOTH, V::VISIBILITY_IN_CATALOG, V::VISIBILITY_IN_SEARCH]);
		/**
		 * 2019-11-22
		 * @uses \Magento\Catalog\Model\ResourceModel\Product\Collection::addMediaGalleryData() loads the collection,
		 * so we should apply filters before it, not after.
		 * «Filters do not work for `catalog`»: https://github.com/justuno-com/m2/issues/5
		 */
		return array_values(array_map(function(P $p) {return [
			'ID' => $p->getId(), 'Variants' => cVariants::p($p)
		];}, $pc->getItems()));
	}, true);}
}