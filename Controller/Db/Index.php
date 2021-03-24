<?php
namespace Justuno\M2\Controller\Db;
use Justuno\M2\Settings as S;
use Magento\Framework\View\Result\Page as R;
# 2021-02-22
# "Implement a database diagnostic tool": https://github.com/justuno-com/core/issues/347
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Index extends \Justuno\Core\Framework\Action {
	/**
	 * 2021-02-22
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return R|null
	 */
	function execute() { /** @var R|null $r */
		if (S::s()->debug()) {
			# 2021-02-25
			# The extension should be `.phtml` instead of `.php`, otherwise `bin/magento setup:di:compile` will be broken:
			# https://github.com/justuno-com/m2/issues/33
			$r = ju_page_result(ju_module_name($this) . '::db.phtml');
		}
		else {
			$r = null;
			ju_403();
		}
		return $r;
	}
}