<?php
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;

/**
 * 2019-11-20
 * 1) It returns a value for the whole row.
 * 2) We should use @uses df_oqi_top() because money amounts are absent for configurable children.
 * 2020-08-26 "Port the `df_oqi_amount` function" https://github.com/justuno-com/core/issues/345
 * @used-by ju_oqi_discount()
 * @used-by ju_oqi_discount_b()
 * @param OI|QI $i
 * @return float
 */
function ju_oqi_amount($i) {
	$k0 = ju_trim_text_left(ju_caller_f(), 'ju_oqi_'); /** @var string $k0 */
	$k1 = ju_trim_text_right($k0, '_b'); /** @var string $k1 */
	$i = ju_oqi_top($i);
	$k = ($k1 === $k0 ? '' : 'base_') . "{$k1}_amount"; /** @var string $k */
	ju_assert($i->offsetExists($k), "[ju_oqi_amount] Invalid key: `$k`.");
	return (float)$i[$k];
}

/**
 * 2019-11-20 It returns a value for the whole row.
 * 2020-08-26 "Port the `df_oqi_discount` function" https://github.com/justuno-com/core/issues/337
 * @see ju_oqi_discount_b()
 * @used-by ju_oqi_price()
 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
 * @param OI|QI $i
 * @return float
 */
function ju_oqi_discount($i) {return ju_oqi_amount($i);}

/**
 * 2019-11-20 It returns a value for the whole row.
 * 2020-08-26 "Port the `df_oqi_discount_b` function" https://github.com/justuno-com/core/issues/339
 * @see ju_oqi_discount()
 * @used-by ju_oqi_price()
 * @param OI|QI $i
 * @return float
 */
function ju_oqi_discount_b($i) {return ju_oqi_amount($i);}

/**
 * 2016-09-07
 * Если товар является настраиваемым, то @uses \Magento\Sales\Model\Order::getItems()
 * будет содержать как настраиваемый товар, так и его простой вариант.
 * Настраиваемые товары мы отфильтровываем.
 *
 * 2017-01-31
 * Добавил @uses array_values(), чтобы функция возвращала именно mixed[], а не array(itemId => mixed).
 * Это важно, потому что эту функцию мы используем только для формирования запросов к API платёжных систем,
 * а этим системам порой (например, Klarna) не всё равно,
 * получат они в JSON-запросе массив или хэш с целочисленными индексами.
 *
 * array_values() надо применять именно после array_filter(),
 * потому что array_filter() создаёт дыры в индексах результата.
 *
 * 2017-02-02
 * Отныне функция упорядочивает позиции заказа по имени.
 * Ведь эта функция используется только для передачи позиций заказа в платежные системы,
 * а там они отображаются покупателю и администратору, и удобно, чтобы они были упорядочены по имени.
 *
 * 2020-08-26 "Port the `df_oqi_leafs` function" https://github.com/justuno-com/core/issues/331
 *
 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
 *
 * @param O|Q $oq
 * @param \Closure|null $f [optional]
 * @param string|null $locale [optional] Используется для упорядочивания элементов.
 * @return array(int => mixed)|OI[]|QI[]
 */
function ju_oqi_leafs($oq, \Closure $f = null, $locale = null) {
	$r = ju_sort_names(array_values(array_filter(
		$oq->getItems(), function($i) {/** @var OI|QI $i */ return ju_oqi_is_leaf($i);}
	)), $locale, function($i) {/** @var OI|QI $i */ return $i->getName();}); /** @var OI[]|QI[] $r */
	/**
	 * 2020-02-04
	 * If we got here from the `sales_order_place_after` event, then the order is not yet saved,
	 * and order items are not yet associated with the order.
	 * I associate order items with the order manually to make @see OI::getOrder() working properly.
	 */
	if (ju_is_o($oq)) {
		foreach ($r as $i) {/** @var OI $i */
			if (!$i->getOrderId()) {
				$i->setOrder($oq);
			}
		}
	}
	return !$f ? $r : array_map($f, $r);
}

/**
 * 2016-05-03
 * Заметил, что у order item, которым соответствуют простые варианты настраиваемого товара,
 * цена почему-то равна нулю и содержится в родительском order item.
 *
 * 2016-08-17 Цена возвращается в валюте заказа (не в учётной валюте системы).
 *
 * 2017-02-01
 * Замечение №1
 * Кроме @uses \Magento\Sales\Model\Order\Item::getPrice()
 * есть ещё метод @see \Magento\Sales\Model\Order\Item::getPriceInclTax().
 * Мы используем именно getPrice(), потому что налоги нам удобнее указывать отдельной строкой,
 * а не размазывать их по товарам.
 * How is getPrice() calculated for an order item? https://mage2.pro/t/2576
 * How is getPriceInclTax() calculated for an order item? https://mage2.pro/t/2577
 * How is getRowTotal() calculated for an order item? https://mage2.pro/t/2578
 * How is getRowTotalInclTax() calculated for an order item?  https://mage2.pro/t/2579
 *
 * Замечение №2
 * Функция возвращает именно стоимость одной единицы товара, а не стоимость строки заказа
 * (потому что использует getPrice(), а не getRowTotal()).
 *
 * 2017-02-02
 * Оказывается, @uses \Magento\Sales\Model\Order\Item::getPrice()
 * может возвращать не число, а строку.
 * И тогда если $i — это вариант настраиваемого товара, то getPrice() вернёт строку «0.0000».
 * Следующей неожиданностью является то, что операция ! для такой строки возвращает false.
 * !"0.0000" === false.
 * И наша функция перестаёт корректно работать.
 * По этой причине стал использовать @uses floatval()
 *
 * 2017-09-25 The function returns the product unit price, not the order row price.
 * 2017-09-30
 * I have added the $afterDiscount flag.
 * It is used oly for the shopping cart price rules.
 * $afterDiscount = false: the functon will return a result BEFORE discounts subtraction.
 * $afterDiscount = true: the functon will return a result AFTER discounts subtraction.
 * For now, I use $afterDiscount = true only for Yandex.Market:
 * @used-by \Dfe\YandexKassa\Charge::pTaxLeafs()
 * Yandex.Kassa does not provide a possibility to specify the shopping cart discounts in a separayte row,
 * so I use $afterDiscount = true.
 *
 * 2020-08-26 "Port the `df_oqi_price` function" https://github.com/justuno-com/core/issues/335
 *
 * @used-by ju_oqi_price()
 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
 *
 * @param OI|QI $i
 * @param bool $withTax [optional]
 * @param bool $withDiscount [optional]
 * @return float
 */
function ju_oqi_price($i, $withTax = false, $withDiscount = false) {/** @var float $r */
	$r = floatval($withTax ? $i->getPriceInclTax() : (
		ju_is_oi($i) ? $i->getPrice() :
			# 2017-04-20 У меня $i->getPrice() для quote item возвращает значение в учётной валюте: видимо, из-за дефекта ядра.
			ju_currency_convert_from_base($i->getBasePrice(), $i->getQuote()->getQuoteCurrencyCode())
	)) ?: ($i->getParentItem() ? ju_oqi_price($i->getParentItem(), $withTax) : .0);
	return !$withDiscount ? $r : ($r - (ju_is_oi($i) ? ju_oqi_discount($i) :
		ju_currency_convert_from_base(ju_oqi_discount_b($i), $i->getQuote()->getQuoteCurrencyCode())
	) / ju_oqi_qty($i));
}

/**
 * 2017-03-06
 * 2020-08-26 "Port the `ju_oqi_qty` function" https://github.com/justuno-com/core/issues/340
 * @used-by ju_oqi_price()
 * @param OI|QI $i
 * @return int
 */
function ju_oqi_qty($i) {return intval(ju_is_oi($i) ? $i->getQtyOrdered() : (ju_is_qi($i) ? $i->getQty() : ju_error()));}

/**
 * 2016-08-18
 * 2020-08-26 "Port the `df_oqi_top` function" https://github.com/justuno-com/core/issues/346
 * @used-by ju_oqi_amount()
 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
 * @param OI|QI $i
 * @return OI|QI
 */
function ju_oqi_top($i) {return $i->getParentItem() ?: $i;}