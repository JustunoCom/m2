<?php
use Magento\Catalog\Model\Product as P;
use Magento\Review\Model\Review\Summary as RS;
/**
 * 2019-11-20
 * 2020-08-24 "Port the `df_review_summary` function" https://github.com/justuno-com/core/issues/326
 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
 * @param P|int $p
 * @return RS
 */
function ju_review_summary($p) {
	$r = ju_new_om(RS::class); /** @var RS $r */
	$r->load(ju_product_id($p));
	return $r;
}