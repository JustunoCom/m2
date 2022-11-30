<?php
namespace Justuno\M2\Plugin\Framework\App\Router;
use Justuno\M2\Controller\Js;
use Magento\Framework\App\Router\ActionList as Sb;
# 2020-03-14
# "Respond to the `/justuno/service-worker.js` request with the provided JavaScript":
# https://github.com/justuno-com/m2/issues/10
# 2020-03-15
# "Replace the `/justuno/service-worker.js` URL with  `/apps/justuno/service-worker.js`":
# https://github.com/justuno-com/m2/issues/11
final class ActionList {
	/**
	 * 2020-03-14
	 * @see \Magento\Framework\App\Router\ActionList::get()
	 * https://github.com/magento/magento2/blob/2.0.0/lib/internal/Magento/Framework/App/Router/ActionList.php#L63-L94
	 * https://github.com/magento/magento2/blob/2.3.4/lib/internal/Magento/Framework/App/Router/ActionList.php#L82-L114
	 * @param string|null $area
	 * @return string|null
	 */
	function aroundGet(Sb $sb, \Closure $f, string $m, $area, string $ns, string $action) {return
		$m === ju_module_name($this) && ju_ends_with($action, '.js') ? Js::class : $f($m, $area, $ns, $action)
	;}
}