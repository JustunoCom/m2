<?php
namespace Justuno\Core\Checkout\Model;
/**
 * 2017-11-17
 * @method string|null getLastRealOrderId()  
 * 2019-11-16
 * @method int|null getLastOrderId()
 * 		getLastRealOrderId() returns the increment ID
 * 		getLastOrderId() returns the numeric ID
 * @see \Magento\Checkout\Model\Type\Onepage::saveOrder()
 * 2020-08-24 "Port the `Df\Checkout\Model\Session` class" https://github.com/justuno-com/core/issues/313
 */
class Session extends \Magento\Checkout\Model\Session {}