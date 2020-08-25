<?php
namespace Justuno\M2\Controller\Response;
use Justuno\Core\Framework\W\Result\Json;
use Justuno\M2\Filter;
use Justuno\M2\Response as R;
use Magento\Customer\Model\Customer as C;
use Magento\Framework\App\Action\Action as _P;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Address as A;
use Magento\Sales\Model\Order\Item as OI;
# 2019-11-20
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Orders extends _P {
	/**
	 * 2019-11-20
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Json
	 */
	function execute() {return R::p(function() {return array_values(array_map(function(O $o) {return [
		'CountryCode' => $o->getBillingAddress()->getCountryId()
		,'CreatedAt' => $o->getCreatedAt()
		,'Currency' => $o->getOrderCurrencyCode()
		/**
		 * 2019-10-31
		 * Orders: «if the customer checked out as a guest
		 * we need still need a Customer object and it needs the ID to be a randomly generated UUID
		 * or other random string»: https://github.com/justuno-com/m1/issues/30
		 */
		,'Customer' => self::customer($o)
		/**
		 * 2019-10-31
		 * Orders: «if the customer checked out as a guest
		 * we need still need a Customer object and it needs the ID to be a randomly generated UUID
		 * or other random string»: https://github.com/justuno-com/m1/issues/30
		 */
		,'CustomerId' => $o->getCustomerId() ?: $o->getCustomerEmail()
		,'Email' => $o->getCustomerEmail()
		,'ID' => $o->getIncrementId()
		,'IP' => $o->getRemoteIp()
		,'LineItems' => df_oqi_leafs($o, function(OI $i) {return [
			'OrderId' => $i->getOrderId()
			# 2019-10-31
			# Orders: «lineItem prices currently being returned in the orders feed are 0 always»:
			# https://github.com/justuno-com/m1/issues/31
			,'Price' => df_oqi_price($i)
			,'ProductId' => (string)df_oqi_top($i)->getProductId()
			,'TotalDiscount' => df_oqi_discount($i)
			# 2019-10-31
			# Orders: «VariantID for lineItems is currently hardcoded as ''»:
			# https://github.com/justuno-com/m1/issues/29
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
	];}, Filter::p(df_order_c())->getItems()));}, true);}
	
	/**
	 * 2019-10-27
	 * 2019-10-31
	 * Orders: «if the customer checked out as a guest
	 * we need still need a Customer object and it needs the ID to be a randomly generated UUID
	 * or other random string»: https://github.com/justuno-com/m1/issues/30
	 * @used-by p()
	 * @param O $o
	 * @return array(string => mixed)
	 */
	private static function customer(O $o) {
		$c = ju_new_om(C::class); /** @var C $c */
		if ($o->getCustomerId()) {
			$c->load($o->getCustomerId());
		}
		$ba = $o->getBillingAddress(); /** @var A $ba */
		return [
			'Address1' => $ba->getStreetLine(1)
			,'Address2' => $ba->getStreetLine(2)
			,'City' => $ba->getCity()
			,'CountryCode' => $ba->getCountryId()
			,'CreatedAt' => $c['created_at']
			,'Email' => $o->getCustomerEmail()
			,'FirstName' => $o->getCustomerFirstname()
			/**
			 * 2019-10-31
			 * Orders: «if the customer checked out as a guest
			 * we need still need a Customer object and it needs the ID to be a randomly generated UUID
			 * or other random string»: https://github.com/justuno-com/m1/issues/30
			 */
			,'ID' => $o->getCustomerId() ?: $o->getCustomerEmail()
			,'LastName' => $o->getCustomerLastname()
			,'OrdersCount' => (int)self::stat($o, 'COUNT(*)')
			,'ProvinceCode' => $ba->getRegionCode()
			,'Tags' => ''
			,'TotalSpend' => (float)self::stat($o, 'SUM(grand_total)')
			,'UpdatedAt' => $c['updated_at']
			,'Zip' => $ba->getPostcode()
		];
	}

	/**
	 * 2019-11-07
	 * 2019-11-07
	 * 1) «Allowed memory size exausted» on `'OrdersCount' => $oc->count()`:
	 * https://github.com/justuno-com/m1/issues/36
	 * 2) I have replaced the customer collection with direct SQL queries.
	 * @used-by ordersCount()
	 * @used-by totalSpent()
	 * @param O $o
	 * @param string $v
	 * @return string
	 */
	private static function stat(O $o, $v) {
		$k = $o->getCustomerId() ? 'customer_id' : 'customer_email'; /** @var string $k */
		return df_fetch_one('sales_order', ['v' => $v], [$k => $o[$k]]);
	}	
}