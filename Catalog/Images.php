<?php
namespace Justuno\M2\Catalog;
use Magento\Catalog\Model\Product as P;
// 2019-10-30
final class Images {
	/**
	 * 2019-10-30
	 * @used-by \Justuno_Jumagext_ResponseController::catalogAction()
	 * @param P $p
	 * @return array(array(string => mixed))
	 */
	static function p(P $p) { /** @var array(array(string => mixed)) $r */
		$r = [];
		// 2019-20-31
		// «Faster way to load media images in a product collection»: https://magento.stackexchange.com/a/153570
		//$p->getResource()->getAttribute('media_gallery')->getBackend()->afterLoad($p);
		// 2019-10-30
		// «"ImageURL" should be "imageURL1" and we should have "imageURL2" and "ImageURL3"
		// if there are image available»: https://github.com/justuno-com/m1/issues/17
		foreach (array_values($p->getMediaGalleryImages()->getItems()) as $idx => $i) {$idx++;
			// 2019-10-30
			// «the feed currently links to the large version of the first image only.
			// Could we change it to link to the small image?»: https://github.com/justuno-com/m1/issues/18
			$r["ImageURL$idx"] = (string)df_catalog_image_h()
				->init($p, 'image', $i['file'])
				->keepAspectRatio(true)
				->constrainOnly(true)
				->keepFrame(false)
				->resize(200, 200)
			;
		}
		return $r;
	}
}