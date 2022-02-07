<?php
use Magento\Framework\Cache\FrontendInterface as IFrontend;

/**
 * 2017-06-30 «How does `Flush Cache Storage` work?» https://mage2.pro/t/4118
 * 2021-03-06 "Port the `df_cache_clean` function": https://github.com/justuno-com/core/issues/354
 * @see \Magento\Backend\Controller\Adminhtml\Cache\FlushAll::execute()
 * @deprecated It is unused.
 */
function ju_cache_clean() {
	ju_map(function(IFrontend $f) {$f->getBackend()->clean();}, ju_cache_pool());
	ju_ram()->reset();
	/**
	 * 2017-10-19
	 * It is important, because M2 caches the configuration values in RAM:
	 * @see \Magento\Config\App\Config\Type\System::get()
	 */
	ju_cfg_m()->clean();
}