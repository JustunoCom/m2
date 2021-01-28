<?php
namespace Justuno\M2;
use Justuno\Core\Exception as DFE;
use Justuno\Core\Framework\W\Result\Json;
use Magento\Framework\App\Config\ScopeConfigInterface as IScopeConfig;
use Magento\Framework\DB\Select as Sel;
use Magento\Store\Api\Data\StoreInterface as IS;
use Magento\Store\Model\ScopeInterface as SS;
# 2019-10-30
final class Response {
	/**
	 * 2019-11-20
	 * @used-by \Justuno\M2\Controller\Cart\Add::execute()
	 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
	 * @used-by \Justuno\M2\Controller\Response\Inventory::execute()
	 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
	 * @param \Closure $f
	 * @param bool $auth [optional]
	 * @return Json
	 */
	static function p(\Closure $f, $auth = true) {/** @var array(string => mixed) $r */
		try {$r = !$auth ? $f() : $f(self::store());}
		catch (\Exception $e) {$r = ['message' => $e->getMessage()];}
		return Json::i(is_null($r) ? 'OK' : (!is_array($r) ? $r : self::filter($r)));
	}

	/**
	 * 2019-10-30 «if a property is null or an empty string do not send it back»: https://github.com/justuno-com/m1/issues/9
	 * @used-by filter()
	 * @used-by p()
	 * @param array(string => mixed) $a
	 * @return array(string => mixed)
	 */
	private static function filter(array $a) {
		$r = []; /** @var array(string => mixed) $r */
		foreach ($a as $k => $v) { /** @var string $k */ /** @var mixed $v */
			if (!in_array($v, ['', null], true)) {
				$r[$k] = !is_array($v) ? $v : self::filter($v);
			}
		}
		return $r;
	}

	/**
	 * 2021-01-28
	 * @used-by p()
	 * @return IS
	 * @throws DFE
	 */
	private static function store() {/** @var IS $r */
		if (!($token = ju_request_header('Authorization'))) { /** @var string|null $token */
			$r = ju_my_local() ? ju_store() : ju_error('Please provide a valid token key');
		}
		else {
			$sel = ju_db_from('core_config_data', ['scope', 'scope_id']); /** @var Sel $sel */
			$sel->where('? = path', 'justuno_settings/options_interface/accid');
			$sel->where('? = value', $token);
			$w = function(array $a) {return jutr(jua($a, 'scope'), array_flip([
				SS::SCOPE_STORES, SS::SCOPE_WEBSITES, IScopeConfig::SCOPE_TYPE_DEFAULT
			]));};
			/** @var array(string => string) $row */
			$row = ju_first(ju_sort(ju_conn()->fetchAll($sel), function(array $a, array $b) use($w) {return $w($a) - $w($b);}));
			ju_assert($row, "The token $token is not registered in Magento.");
			$scope = jua($row, 'scope'); /** @var string $scope */
			$scopeId = jua($row, 'scope_id'); /** @var string $scopeId */
			$r = SS::SCOPE_STORES === $scope ? ju_store($scopeId) : (
				IScopeConfig::SCOPE_TYPE_DEFAULT === $scope ? ju_store() :
					ju_store_m()->getWebsite($scopeId)->getDefaultStore()
			);
		}
		return $r;
	}
}