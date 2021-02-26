<?php
namespace Justuno\M2\Controller\Cart;
use Justuno\Core\Framework\W\Result\Json;
use Justuno\M2\Response as R;
use Magento\Catalog\Model\Product as P;
use Magento\Framework\App\Action\Action as _P;
# 2020-01-21 "Implement the «add a configurable product to the cart» endpoint": https://github.com/justuno-com/m2/issues/7
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Add extends _P {
	/**
	 * 2020-01-21
	 * @see \Magento\Checkout\Controller\Cart\Add::execute()
	 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Checkout/Controller/Cart/Add.php#L77-L178
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Json
	 */
	function execute() {return R::p(function() {
		/**
		 * 2020-01-21
		 * @see \Magento\Checkout\Controller\Cart\Add::_initProduct()
		 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Checkout/Controller/Cart/Add.php#L56-L75
		 */
		$p = self::product('product'); /** @var P $p */
		$params = ['product' => $p->getId(), 'qty' => ju_nat(ju_request('qty', 1))];
		if (ju_configurable($p)) {
			$ch = self::product('variant'); /** @var P $ch */
			$sa = []; /** @var array(int => int) $sa */
			/**
			 * 2020-01-27
			 * 1) In Magento 2, the @uses \Magento\Catalog\Model\Product::getTypeInstance() method does not have arguments:
			 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Catalog/Model/Product.php#L628-L640
			 * It always returns a singleton:
			 * 1.1) @see \Magento\Catalog\Model\Product\Type::factory():
			 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Catalog/Model/Product/Type.php#L114-L135
			 * 1.2) @see \Magento\Catalog\Model\Product\Type\Pool::get()
			 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Catalog/Model/Product/Type/Pool.php#L31-L49
			 * 2) In Magento 1, the method has an optional $singleton argument with the default `false` value:
			 * https://github.com/OpenMage/magento-mirror/blob/1.9.4.5/app/code/core/Mage/Catalog/Model/Product.php#L252-L275
			 */
			foreach ($p->getTypeInstance()->getConfigurableAttributesAsArray($p) as $a) {/** @var array(string => mixed) $a */
				$sa[(int)$a['attribute_id']] = $ch[$a['attribute_code']];
			}
			$params['super_attribute'] = $sa;
		}
		ju_cart()->addProduct($p, $params);
		ju_cart()->save();
		ju_dispatch('checkout_cart_add_product_complete', [
			'product' => $p, 'request' => $this->getRequest(), 'response' => $this->getResponse()
		]);
	});}

	/**
	 * 2020-01-21
	 * @used-by execute()
	 * @param string $k
	 * @return P
	 */
	private static function product($k) {return ju_product(ju_nat(ju_request($k)), true);}
}