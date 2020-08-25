<?php
namespace Justuno\M2\Controller\Cart;
use Justuno\Core\Framework\W\Result\Json;
use Justuno\M2\Response as R;
use Magento\Catalog\Model\Product as P;
use Magento\Framework\App\Action\Action as _P;
# 2020-01-21
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Add extends _P {
	/**
	 * 2020-01-21
	 * "Implement the «add a configurable product to the cart» endpoint": https://github.com/justuno-com/m2/issues/7
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
			foreach ($p->getTypeInstance(true)->getConfigurableAttributesAsArray($p) as $a) {/** @var array(string => mixed) $a */
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
	private static function product($k) {return df_product(ju_nat(ju_request($k)), true);}
}