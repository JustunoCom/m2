<?php
use Justuno\Core\RAM;
use Magento\Framework\App\Cache\Frontend\Pool;
/**
 * 2017-06-30
 * @used-by ju_cache_clean()
 * @return Pool
 */
function ju_cache_pool() {return ju_o(Pool::class);}

/**
 * 2020-06-13 "Port the `df_ram` function": https://github.com/justuno-com/core/issues/8
 * @used-by ju_cache_clean()
 * @used-by jucf()
 * @return RAM
 */
function ju_ram() {return RAM::s();}