<?php
use Magento\Quote\Model\Quote as Q;
use Magento\Quote\Model\Quote\Item as QI;
use Magento\Sales\Model\Order as O;
use Magento\Sales\Model\Order\Item as OI;

/**
 * 2017-04-10
 * 2020-06-24 "Port the `ju_is_o` function": https://github.com/justuno-com/core/issues/123
 * @used-by ju_is_oq()
 * @used-by ju_oqi_leafs()
 * @used-by ju_store()
 * @param mixed $v
 * @return bool
 */
function ju_is_o($v) {return $v instanceof O;}

/**
 * 2017-04-20
 * 2020-08-24 "Port the `df_is_oi` function" https://github.com/justuno-com/core/issues/321
 * @used-by ju_is_oqi()
 * @used-by ju_oqi_is_leaf()
 * @used-by ju_oqi_price()
 * @used-by ju_oqi_qty()
 * @param mixed $v
 * @return bool
 */
function ju_is_oi($v) {return $v instanceof OI;}

/**
 * 2017-04-08
 * 2020-08-26 "Port the `ju_is_oq` function" https://github.com/justuno-com/core/issues/343
 * @used-by ju_currency_base()
 * @param mixed $v
 * @return bool
 */
function ju_is_oq($v) {return ju_is_o($v) || ju_is_q($v);}

/**
 * 2020-02-05
 * 2020-08-24 "Port the `df_is_oqi` function" https://github.com/justuno-com/core/issues/319
 * @used-by ju_product()
 * @param mixed $v
 * @return bool
 */
function ju_is_oqi($v) {return ju_is_oi($v) || ju_is_qi($v);}

/**
 * 2017-04-10
 * 2020-08-26 "Port the `df_is_q` function" https://github.com/justuno-com/core/issues/344
 * @used-by ju_is_oq()
 * @param mixed $v
 * @return bool
 */
function ju_is_q($v) {return $v instanceof Q;}

/**
 * 2017-04-20
 * 2020-08-24 "Port the `df_is_qi` function" https://github.com/justuno-com/core/issues/322
 * @used-by ju_is_oqi()
 * @used-by ju_oqi_is_leaf()
 * @used-by ju_oqi_qty()
 * @param mixed $v
 * @return bool
 */
function ju_is_qi($v) {return $v instanceof QI;}

/**
 * 2017-04-20
 * 2020-08-26 "Port the `ju_oqi_is_leaf` function" https://github.com/justuno-com/core/issues/333
 * @used-by ju_oqi_is_leaf()
 * @param OI|QI $i
 * @return bool
 */
function ju_oqi_is_leaf($i) {return ju_is_oi($i) ? !$i->getChildrenItems() : (ju_is_qi($i) ? !$i->getChildren() : ju_error());}