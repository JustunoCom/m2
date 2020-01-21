<?php
namespace Justuno\M2\Controller\Cart;
use Df\Framework\W\Result\Json;
use Justuno\M2\Response as R;
use Magento\Framework\App\Action\Action as _P;
// 2020-01-21
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Add extends _P {
	/**
	 * 2020-01-21
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Json
	 */
	function execute() {return R::p(function() {});}
}