<?php
namespace Justuno\M2\Block;
use Justuno\M2\Settings as S;
use Magento\Framework\View\Element\AbstractBlock as _P;
use Magento\Sales\Model\Order as O;
# 2019-11-15
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Js extends _P {
	/**
	 * 2019-11-15
	 * @override
	 * @see _P::_toHtml()
	 * @used-by _P::toHtml():
	 *		$html = $this->_loadCache();
	 *		if ($html === false) {
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->suspend($this->getData('translate_inline'));
	 *			}
	 *			$this->_beforeToHtml();
	 *			$html = $this->_toHtml();
	 *			$this->_saveCache($html);
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->resume();
	 *			}
	 *		}
	 *		$html = $this->_afterToHtml($html);
	 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L643-L689
	 * @return string
	 */
	final protected function _toHtml() { /** @var string $r */
		if (!ju_is_guid($id = S::s()->accid())) {
			$r = '';
		}
		else {
			$p = ['merchantId' => $id]; /** @var array(string => mixed) $p */
			if (ju_is_catalog_product_view()) {
				$p += ['action' => ju_action_name(), 'productId' => ju_product_current_id()];
			}
			elseif (ju_is_checkout_success()) {
				$o = ju_order_last(); /** @var O $o */
				$p += ['currency' => $o->getOrderCurrencyCode(), 'orderId' => $o->getId(), 'order' => [
					'orderID' =>  $o->getId()
					,'grandTotal' => $o->getGrandTotal()
					,'subtotal' => $o->getSubtotal()
					,'tax' => $o->getTaxAmount()
					,'shipping' => $o->getShippingAmount()
					,'discount' => $o->getDiscountAmount()
					,'currency' => $o->getOrderCurrencyCode()
					,'discountCodes'=> [$o->getCouponCode()]

				]];
			}
			$r = ju_js(__CLASS__, '', $p);
		}
		return $r;
	}
}