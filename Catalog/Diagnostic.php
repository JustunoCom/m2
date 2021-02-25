<?php
namespace Justuno\M2\Catalog;
use Justuno\Core\Exception as DFE;
use Justuno\M2\Store as S;
# 2021-02-25
# "Provide a diagnostic message if the requested product is not eligible for the feed":
# https://github.com/justuno-com/m2/issues/32
final class Diagnostic {
	/**
	 * 2021-02-25
	 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
	 * @throws DFE
	 */
	static function p() {
		$id = (int)ju_request('id'); /** @var int $id */
		if (!ju_fetch_one('catalog_product_entity', 'entity_id', ['entity_id' => $id])) {
			ju_error("The product with id «{$id}» is absent in the `catalog_product_entity` table.");
		}
		$ww = ju_fetch_col_int('catalog_product_website', 'website_id', 'product_id', $id); /** @var int[] $ww */
		if (!in_array($w = S::v()->getWebsiteId(), $ww)) { /** @var int $w */
			ju_error(
				"The product with id «{$id}» is not eligible for the feed because it is associated with websites [%s],"
				. " but the feed is generated for the website «{$w}» (according to the token provided)."
				,ju_csv_pretty(ju_quote_russian($ww))
			);
		}
		ju_error('The product is not eligible for the feed for an unknown reason.');
	}
}