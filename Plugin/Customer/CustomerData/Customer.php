<?php
namespace Justuno\M2\Plugin\Customer\CustomerData;
use Magento\Customer\CustomerData\Customer as Sb;
use Magento\Customer\Helper\Session\CurrentCustomer as C;
// 2019-11-17
final class Customer {
	/**
	 * 2019-11-17
	 * @see \Magento\Customer\CustomerData\Customer::getSectionData()
	 * @param Sb $sb
	 * @param array(string => mixed) $r
	 * @return array(string => mixed)
	 */
	function afterGetSectionData(Sb $sb, array $r) {
		$c = df_o(C::class); /** @var C $c */
		return ['id' => $c->getCustomerId()] + $r;
	}
}