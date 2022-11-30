<?php
namespace Justuno\M2\Controller\Response;
use Justuno\Core\Framework\W\Result\Json;
use Justuno\M2\Filter;
use Justuno\M2\Response as R;
use Justuno\M2\Store;
use Magento\Framework\App\Action\Action as _P;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;
/** 2019-11-20 @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Orders extends _P {
	/**
	 * 2019-11-20
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 */
	function execute():Json {return R::p(function():array {return array_values(array_map(function(O $o) {return [
		'CountryCode' => $o->getBillingAddress()->getCountryId()
		,'CreatedAt' => $o->getCreatedAt()
		,'Currency' => $o->getOrderCurrencyCode()
		# 2019-10-31
		# Orders: «if the customer checked out as a guest
		# we need still need a Customer object and it needs the ID to be a randomly generated UUID
		# or other random string»: https://github.com/justuno-com/m1/issues/30
		# 2021-02-05 «on the orders feed, remove the Customers object entirely»: https://github.com/justuno-com/m2/issues/26
		# 2019-10-31
		# Orders: «if the customer checked out as a guest
		# we need still need a Customer object and it needs the ID to be a randomly generated UUID
		# or other random string»: https://github.com/justuno-com/m1/issues/30
		,'CustomerId' => $o->getCustomerId() ?: $o->getCustomerEmail()
		,'Email' => $o->getCustomerEmail()
		,'ID' => $o->getIncrementId()
		,'IP' => $o->getRemoteIp()
		,'LineItems' => ju_oqi_leafs($o, function(OI $i) {return [
			'OrderId' => $i->getOrderId()
			# 2019-10-31
			# Orders: «lineItem prices currently being returned in the orders feed are 0 always»:
			# https://github.com/justuno-com/m1/issues/31
			,'Price' => ju_oqi_price($i)
			,'ProductId' => (string)ju_oqi_top($i)->getProductId()
			,'TotalDiscount' => ju_oqi_discount($i)
			# 2019-10-31
			# Orders: «VariantID for lineItems is currently hardcoded as ''»: https://github.com/justuno-com/m1/issues/29
			,'VariantId' => $i->getProductId()
		];})
		,'OrderNumber' => $o->getId()
		,'ShippingPrice' => (float)$o->getShippingAmount()
		,'Status' => $o->getStatus()
		,'SubtotalPrice' => (float)$o->getSubtotal()
		,'TotalDiscounts' =>(float) $o->getDiscountAmount()
		,'TotalItems' => (int)$o->getTotalItemCount()
		,'TotalPrice' => (float)$o->getGrandTotal()
		,'TotalTax' => (float)$o->getTaxAmount()
		,'UpdatedAt' => $o->getUpdatedAt()
	# 2021-01-28 "Make the module multi-store aware": https://github.com/justuno-com/m2/issues/24
	];}, Filter::p(ju_order_c()->addFieldToFilter('store_id', Store::v()->getId()))->getItems()));});}
}