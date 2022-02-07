<?php
/**
 * 2016-08-24
 * 2020-08-23 "Port the `df_handle` function" https://github.com/justuno-com/core/issues/299
 * @used-by ju_is_catalog_product_view()
 * @used-by ju_is_checkout_success()
 * @param string $n
 * @return bool
 */
function ju_handle($n) {return in_array($n, ju_handles());}

/**
 * 2015-12-21
 * 2020-08-23 "Port the `df_handles` function" https://github.com/justuno-com/core/issues/300
 * @used-by ju_handle()
 * @return string[]
 */
function ju_handles() {return ($u = ju_layout_update(null)) ? $u->getHandles() : [];}