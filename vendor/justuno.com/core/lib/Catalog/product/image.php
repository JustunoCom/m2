<?php
use Magento\Catalog\Helper\Image as ImageH;

/**
 * 2016-04-23
 * 2020-08-23 "Port the `df_catalog_image_h` function" https://github.com/justuno-com/core/issues/296
 * @used-by \Justuno\M2\Catalog\Images::p()
 * @return ImageH
 */
function ju_catalog_image_h() {return ju_o(ImageH::class);}