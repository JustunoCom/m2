<?php
use Magento\Sales\Model\ResourceModel\Order\Collection as C;
/**
 * 2019-11-20
 * 2020-08-26 "Port the `df_order_c` function" https://github.com/justuno-com/core/issues/330
 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
 * @return C
 */
function ju_order_c() {return ju_new_om(C::class);}