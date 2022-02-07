<?php
use Magento\Catalog\Api\ProductRepositoryInterface as IProductRepository;
use Magento\Catalog\Model\Product as P;
use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\Exception\NoSuchEntityException as NSE;
use Magento\Framework\Exception\NotFoundException as NotFound;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order\Item as OI;
use Magento\Store\Api\Data\StoreInterface as IStore;
/**
 * 2019-02-26
 * 2019-05-15 I have added the $s parameter: https://magento.stackexchange.com/a/177164
 * 2019-09-20
 * I tried to support SKU as $p using the following way:
 *	call_user_func(
 *		[df_product_r(), ctype_digit($p) || df_is_oi($p) ? 'getById' : 'get']
 *		,df_is_oi($p) ? $p->getProductId() : $p
 *		...
 *	)
 * https://github.com/mage2pro/core/commit/01d4fbbf83
 * It was wrong because SKU can be numeric, so the method become ambiguous.
 * Use @see \Magento\Catalog\Model\ProductRepository::get() directly to load a product by SKU, e.g.:
 * 		df_product_r()->get('your SKU')
 * 2020-08-24 "Port the `df_product` function" https://github.com/justuno-com/core/issues/317
 * @see df_category()
 * @see df_product_load()
 * @used-by \Justuno\M2\Controller\Cart\Add::product()
 * @param int|string|P|OI|QI $p
 * @param int|string|null|bool|IStore $s [optional]
 * @return P
 * @throws NSE
 */
function ju_product($p, $s = false) {return $p instanceof P ? $p : ju_product_r()->getById(
	/**
	 * 2020-02-05
	 * 1) I do not use @see \Magento\Sales\Model\Order\Item::getProduct()
	 * and @see \Magento\Quote\Model\Quote\Item\AbstractItem::getProduct() here,
	 * because they return `null` for an empty product ID, but df_product() should throw @see NSE in such cases.
	 * 2) Also, my implementation allows to specify a custom $s.
	 */
	ju_is_oqi($p) ? $p->getProductId() : $p
	,false
	,false === $s ? null : ju_store_id(true === $s ? null : $s)
	,true === $s
);}

/**
 * 2018-09-27
 * 2020-08-24 "Port the `df_product_current` function" https://github.com/justuno-com/core/issues/306
 * @used-by ju_product_current_id()
 * @param \Closure|bool|mixed $onError
 * @return P|null
 * @throws NotFound|\Exception
 */
function ju_product_current($onError = null) {return ju_try(function() {return
	ju_is_backend() ? ju_catalog_locator()->getProduct() : (ju_registry('current_product') ?: ju_error())
;}, $onError);}

/**
 * 2019-11-15
 * 2020-08-24 "Port the `df_product_current_id` function" https://github.com/justuno-com/core/issues/305
 * @used-by \Justuno\M2\Block\Js::_toHtml()
 * @return int|null
 */
function ju_product_current_id() {return !($p = ju_product_current() /** @var P $p */) ? null : $p->getId();}

/**
 * 2019-11-18
 * 2020-08-23 "Port the `df_product_id` function" https://github.com/justuno-com/core/issues/278
 * @used-by ju_qty()
 * @used-by ju_review_summary()
 * @param P|int $p
 * @return int
 */
function ju_product_id($p) {return ju_int($p instanceof P ? $p->getId() : $p);}

/**
 * 2019-02-26
 * 2020-08-24 "Port the `ju_product_r` function" https://github.com/justuno-com/core/issues/318
 * @used-by ju_product()
 * @return IProductRepository|ProductRepository
 */
function ju_product_r() {return ju_o(IProductRepository::class);}