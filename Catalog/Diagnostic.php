<?php
namespace Justuno\M2\Catalog;
use Justuno\Core\Exception as DFE;
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
		ju_error('The product is not eligible for the feed for an unknown reason.');
	}
}