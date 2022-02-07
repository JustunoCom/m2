<?php
use Magento\Framework\App\ObjectManager as OM;
use Magento\Framework\ObjectManagerInterface as IOM;

/**
 * 2020-06-13 "Port the `df_o` function": https://github.com/justuno-com/core/issues/3
 * @used-by ju_app_state()
 * @used-by ju_asset()
 * @used-by ju_asset_source()
 * @used-by ju_backend_session()
 * @used-by ju_cache_pool()
 * @used-by ju_cart()
 * @used-by ju_catalog_image_h()
 * @used-by ju_catalog_locator()
 * @used-by ju_cfg_m()
 * @used-by ju_checkout_session()
 * @used-by ju_component_r()
 * @used-by ju_customer_registry()
 * @used-by ju_customer_session()
 * @used-by ju_db_resource()
 * @used-by ju_default_stock_provider()
 * @used-by ju_deployment_cfg()
 * @used-by ju_dispatch()
 * @used-by ju_fs()
 * @used-by ju_layout()
 * @used-by ju_magento_version_m()
 * @used-by ju_message_m()
 * @used-by ju_module_dir_reader()
 * @used-by ju_module_m()
 * @used-by ju_msi_allowed_for_pt()
 * @used-by ju_msi_website2stockId()
 * @used-by ju_new_om()
 * @used-by ju_page_result()
 * @used-by ju_product_r()
 * @used-by ju_qty()
 * @used-by ju_registry_o()
 * @used-by ju_request_o()
 * @used-by ju_response()
 * @used-by ju_scope_resolver_pool()
 * @used-by ju_stock_cfg()
 * @used-by ju_stock_index_table_name_resolver()
 * @used-by ju_stock_r()
 * @used-by ju_store_cookie_m()
 * @used-by ju_store_m()
 * @used-by ju_url_frontend_o()
 * @used-by ju_url_o()
 * @used-by ju_visitor_ip()
 * @used-by \Justuno\Core\Theme\Model\View\Design::isThemeInitialized()
 * @param string $t
 * @return mixed
 */
function ju_o($t) {return jucf(function($t) {return ju_om()->get($t);}, [$t]);}

/**
 * 2020-06-13 "Port the `df_om` function": https://github.com/justuno-com/core/issues/4
 * @used-by ju_o()
 * @return OM|IOM
 */
function ju_om() {return OM::getInstance();}