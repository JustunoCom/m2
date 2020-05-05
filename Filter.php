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
	 * @param C|OC|PC $r
	 * @return OC|PC;
	 */
	static function p(C $r) {
		self::byDate($r);
		self::byProduct($r);
		/** @var string $dir */ /** @var string $suffix */
		list($dir, $suffix) = $r instanceof PC ? ['DESC', 'Products'] : ['ASC', 'Orders'];
		if ($field = df_request("sort$suffix")) { /** @var string $field */
			$r->getSelect()->order("$field $dir");
		}
		$size = (int)df_request('pageSize', 10); /** @var int $size */
		$r->getSelect()->limit($size, $size * ((int)df_request('currentPage', 1) - 1));
		return $r;
	}

	/**
	 * 2019-10-31
	 * @used-by p()
	 * @param C|OC|PC $c
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

	/**
	 * 2020-05-06
	 * "Provide an ability to filter the `jumagext/response/catalog` response by a concrete product":
	 * https://github.com/justuno-com/m2/issues/12
	 * @used-by p()
	 * @param C|OC|PC $c
	 */
	private static function byProduct(C $c) {
		if ($id = df_request('id')) { /** @var string $id */
			$c->addFieldToFilter('entity_id', $id);
		}
		if ($name = df_request('title')) { /** @var string $name */
			/**
			 * 2020-05-06
			 * @uses \Magento\Eav\Model\Entity\Collection\AbstractCollection::addFieldToFilter()
			 * works even if the Flat Mode is disabled because it just delegates the work to
			 * @see \Magento\Eav\Model\Entity\Collection\AbstractCollection::addAttributeToFilter():
			 *	public function addFieldToFilter($attribute, $condition = null) {
			 *		return $this->addAttributeToFilter($attribute, $condition, 'left');
			 *	}
			 * https://github.com/magento/magento2/blob/2.3.5-p1/app/code/Magento/Eav/Model/Entity/Collection/AbstractCollection.php#L395-L406
			 * https://github.com/magento/magento2/blob/2.0.0/app/code/Magento/Eav/Model/Entity/Collection/AbstractCollection.php#L383-L394
			 */
			$c->addFieldToFilter('name', [['like' => "%$name%"]]);
		}
		if ($sku = df_request('sku')) { /** @var string $sku */
			$c->addFieldToFilter('sku', [['like' => "%$sku%"]]);
		}
	}
}