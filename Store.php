<?php
namespace Justuno\M2;
use Justuno\Core\Exception as DFE;
use Magento\Framework\App\Config\ScopeConfigInterface as IScopeConfig;
use Magento\Framework\DB\Select as Sel;
use Magento\Store\Api\Data\StoreInterface as IS;
use Magento\Store\Model\ScopeInterface as SS;
# 2021-01-28, 2021-02-25 "Make the module multi-store aware": https://github.com/justuno-com/m2/issues/24
final class Store {
	/**
	 * 2021-01-28 "Make the module multi-store aware": https://github.com/justuno-com/m2/issues/24
	 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
	 * @used-by \Justuno\M2\Controller\Response\Inventory::execute()
	 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
	 * @return IS
	 * @throws DFE
	 */
	static function v() {return jucf(function() {
		/** @var IS $r */
		if (!($token = ju_request_header('Authorization'))) { /** @var string|null $token */
			$r = ju_my_local() ? ju_store() : ju_error('Please provide a valid token key');
		}
		else {
			$sel = ju_db_from('core_config_data', ['scope', 'scope_id']); /** @var Sel $sel */
			$sel->where('? = path', 'justuno_settings/options_interface/token_key');
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
		ju_sentry_extra(__CLASS__, 'Store', "{$r->getCode()} ({$r->getId()})");
		return $r;
	});}
}