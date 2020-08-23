<?php
namespace Justuno\M2\Catalog;
use Magento\Catalog\Model\Product as P;
# 2019-10-30
final class Images {
	/**
	 * 2019-10-30
	 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
	 * @param P $p
	 * @return array(array(string => mixed))
	 */
	static function p(P $p) {return ju_map_kr(function($idx, $i) use($p) {return [
		# 2019-10-30
		# «"ImageURL" should be "imageURL1" and we should have "imageURL2" and "ImageURL3"
		# if there are image available»: https://github.com/justuno-com/m1/issues/17
		'ImageURL' . (1 + $idx), df_catalog_image_h()
			->init($p, 'image', ['type' => 'image'])
			->keepAspectRatio(true)
			->constrainOnly(true)
			->keepFrame(false)
			->setImageFile($i['file'])
			# 2019-10-30
			# «the feed currently links to the large version of the first image only.
			# Could we change it to link to the small image?»: https://github.com/justuno-com/m1/issues/18
			->resize(200, 200)
			->getUrl()
	];}, array_values($p->getMediaGalleryImages()->getItems()));}
}