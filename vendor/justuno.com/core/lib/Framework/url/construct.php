<?php
use Magento\Store\Model\Store;
/**
 * 2015-11-28
 * 2016-12-01 If $path is null, '', or '/', then the function will return the frontend root URL.
 * 2016-12-01 On the frontend side, the @see df_url() behaves identical to df_url_frontend()
 * 2021-03-07 "Port the `df_url_frontend` function": https://github.com/justuno-com/core/issues/363
 * 2021-08-05 @deprecated It is unused.
 * @param string|null $path [optional]
 * @param array(string => mixed) $p [optional]
 * @param Store|int|string|null $store [optional]
 * @return string
 */
function ju_url_frontend($path = null, array $p = [], $store = null) {return ju_url_trim_index(
	ju_url_frontend_o()->getUrl($path, ju_nosid() + $p + (is_null($store) ? [] : ['_store' => ju_store($store)]))
);}