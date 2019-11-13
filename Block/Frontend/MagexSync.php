<?php

namespace Justuno\Jumagext\Block\Frontend;

use Magento\Checkout\Model\Cart;
use Magento\Framework\View\Element\Template;

class MagexSync extends Template
{
	/**
	 * @var Cart
	 */
	protected $cart;

	/**
	 * MagexSync constructor.
	 * @param Cart             $cart
	 * @param Template\Context $context
	 * @param array            $data
	 */
	function __construct(
		Cart $cart,
		Template\Context $context,
		array $data
	) {
		$this->cart = $cart;
		parent::__construct($context, $data);
	}


	/**
	 * @return object
	 *
	 */
	function juGetQuoteData()
	{
		/**
		 * Grab quote data and inject into block for use with custom quote.js
		 * */
		{
			return $this->cart->getQuote();
		}
	}

	/**
	 * @return array
	 *
	 */
	function juGetCartItems()
	{
		/**
		 * Alternatively: $itemsCollection = quote->getItemsCollection();
		 * for a collection
		 * Alternatively: $itemsVisible = quote->getAllItems();
		 * for directly accessible items only
		 * */
		{
			$items = $this->cart->getQuote()->getAllVisibleItems();
			return $items;
		}
	}

	/**
	 *
	 * @return string
	 */
	function juGetCartQty()
	{
		/* alternatively getItemsCount(); */
		if (!$this->cart->getQuote()->getItemsQty()) {
			return 0;
		}
		return $this->cart->getQuote()->getItemsQty();
	}

	/**
	 * @return string
	 */
	function juGetCartGrandTotal()
	{
		return $this->cart->getQuote()->getGrandTotal();
	}

	/**
	 * @return string
	 */
	function juGetCartSubTotal()
	{
		return $this->cart->getQuote()->getSubtotal();
	}

	/**
	 * @return array
	 *
	 */
	function juGetParsedCartItems()
	{
		$items = $this->cart->getQuote()->getAllVisibleItems();
		$cartItems = [];
		$idx = 0;
		foreach ($items as $item) {
			$options = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
			$itemProps = array();
			if(array_key_exists('attributes_info', $options)) {
				$attributes = $options['attributes_info'];
				 foreach($attributes as $attribute) {
					$itemProps[strtolower($attribute['label'])] =  $attribute['value'];
				}
			}
			$cartItems[$idx] = [
				"productid" => $item->getProductId(),
				"variationid" => $item->getItemId(),
				"sku" => $item->getSku(),
				"quantity" => $item->getQty(),
				"price" => $item->getPrice(),
				"name" => $item->getName()]
				+ $itemProps;
				$idx++;
		}
		return $cartItems;
	}
}

