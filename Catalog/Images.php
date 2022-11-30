<?php
namespace Justuno\M2\Catalog;
use Magento\Catalog\Model\Product as P;
use Magento\Framework\DataObject as _DO;
# 2019-10-30
final class Images {
	/**
	 * 2019-10-30
	 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
	 * @return array(array(string => mixed))
	 */
	static function p(P $p):array {return ju_map_kr(function($idx, _DO $i) use($p) {return [
		# 2019-10-30
		# «"ImageURL" should be "imageURL1" and we should have "imageURL2" and "ImageURL3"
		# if there are image available»: https://github.com/justuno-com/m1/issues/17
		'ImageURL' . (1 + $idx), ju_catalog_image_h()
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
	# 2021-02-05
	# «on the parent object, it's returning a lot more than ImageURL1 - 3. I only need the first three»:
	# https://github.com/justuno-com/m2/issues/27
	];}, array_slice(ju_sort(array_values($p->getMediaGalleryImages()->getItems()), function(_DO $a, _DO $b) {
		# 2020-09-29
		# "Images with the «_hero_» string should have a priority in product feeds": https://github.com/justuno-com/m2/issues/17
		$f = function(_DO $i) {return (int)ju_contains($i['file'], '_hero_');};
		return $f($b) - $f($a);
	}), 0, 3));}
}