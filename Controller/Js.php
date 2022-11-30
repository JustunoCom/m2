<?php
namespace Justuno\M2\Controller;
use Justuno\M2\W\Result\Js as R;
use Magento\Framework\App\Action\Action as _P;
/**
 * 2020-03-14
 * "Respond to the `/justuno/service-worker.js` request with the provided JavaScript": https://github.com/justuno-com/m2/issues/10
 * 2020-03-15
 * "Replace the `/justuno/service-worker.js` URL with  `/apps/justuno/service-worker.js`":
 * https://github.com/justuno-com/m2/issues/11
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by \Justuno\M2\Plugin\Framework\App\Router\ActionList::aroundGet()
 */
class Js extends _P {
	/**
	 * 2020-03-14
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 */
	function execute():R {return R::i(ju_strip_ext($this->getRequest()->getActionName()));}
}