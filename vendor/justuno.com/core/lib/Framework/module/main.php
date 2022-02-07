<?php
use Magento\Framework\Module\Manager as MM;
use Magento\Framework\Module\ModuleList as ML;
use Magento\Framework\Module\ModuleListInterface as IML;

/**
 * 2019-11-21
 * 2020-08-23 "Port the `df_module_enabled` function" https://github.com/justuno-com/core/issues/282
 * @used-by ju_msi()
 * @param string $m
 * @return bool
 */
function ju_module_enabled($m) {return ju_module_m()->isEnabled($m);}

/**
 * 2019-11-21
 * 2020-08-23 "Port the `ju_module_m` function" https://github.com/justuno-com/core/issues/283
 * @used-by ju_module_enabled()
 * @return MM
 */
function ju_module_m() {return ju_o(MM::class);}