<?php
namespace Justuno\M2;
use Magento\Catalog\Model\ResourceModel\Product\Collection as PC;
use Magento\Directory\Helper\Data as DirectoryH;
use Magento\Framework\Data\Collection\AbstractDb as C;
use Magento\Sales\Model\ResourceModel\Order\Collection as OC;
// 2019-10-31
final class Filter {
	/**
	 * 2019-10-31
	 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
	 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
	 * @param C|OC|PC $c
	 */
	static function p(C $c) {
		self::byDate($c);
		/** @var string $dir */ /** @var string $suffix */
		list($dir, $suffix) = $c instanceof PC ? ['DESC', 'Products'] : ['ASC', 'Orders'];
		if ($field = df_request("sort$suffix")) { /** @var string $field */
			$c->getSelect()->order("$field $dir");
		}
		$size = (int)df_request('pageSize', 10); /** @var int $size */
		$c->getSelect()->limit($size, $size * ((int)df_request('currentPage', 1) - 1));
	}

	/**
	 * 2019-10-31
	 * @used-by p()
	 * @param $c $c
	 */
	private static function byDate(C $c) {
		if ($since = df_request('updatedSince')) { /** @var string $since */
			/**
			 * 2019-10-31
			 * @param string $s
			 * @return string
			 */
			$d = function($s) {
				$f = 'Y-m-d H:i:s'; /** @var string $f */
				$tz = df_cfg(DirectoryH::XML_PATH_DEFAULT_TIMEZONE); /** @var string $tz */
				$dt = new \DateTime(date($f, strtotime($s)), new \DateTimeZone($tz));	/** @var \DateTime $dt */
				return date($f, $dt->format('U'));
			};
			$c->addFieldToFilter('updated_at', ['from' => $d($since), 'to' => $d('2035-01-01 23:59:59')]);
		}
	}
}