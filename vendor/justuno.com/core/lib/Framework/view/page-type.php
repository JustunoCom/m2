<?php
/**
 * 2019-03-27
 * 2020-08-23 "Port the `df_is_catalog_product_view` function" https://github.com/justuno-com/core/issues/298
 * @used-by \Justuno\M2\Block\Js::_toHtml()
 * @return bool
 */
function ju_is_catalog_product_view() {return ju_handle('catalog_product_view');}

/**
 * 2017-03-29
 * 2017-08-28
 * @todo May be we should use @see df_action() here?
 * @see  df_is_checkout_multishipping()
 * How to detect the «checkout success» page programmatically in PHP? https://mage2.pro/t/3562
 * 2020-08-24 "Port the `df_is_checkout_success` function" https://github.com/justuno-com/core/issues/310
 * @used-by \Justuno\M2\Block\Js::_toHtml()
 * @return bool
 */
function ju_is_checkout_success() {return ju_handle('checkout_onepage_success');}

/**
 * 2017-10-15
 * 2017-12-04
 * The previous code was:
 * 		df_handle('adminhtml_system_config_edit')
 * It is incorrect, because:
 * 1) It does not take into account the `admin/system_config/save` action.
 * 2) We do not have any layout handles yet in a @see \Justuno\Core\Config\Backend::dfSaveAfter() handler:
 * e.g., in the @see \Dfe\Moip\Backend\Enable::dfSaveAfter() handler.
 * So the following code will not help us:
 * 		df_handle_prefix('adminhtml_system_config_')
 * It can be related to the following Moip issue:
 * "«Please set your Moip private key in the Magento backend» even if the Moip private key is set"
 * https://github.com/mage2pro/moip/issues/22
 * 2021-03-06 "Port the `df_is_system_config` function": https://github.com/justuno-com/core/issues/357
 * @used-by \Justuno\Core\Config\Settings::scope()
 * @return bool
 */
function ju_is_system_config() {return ju_action_prefix('adminhtml_system_config');}