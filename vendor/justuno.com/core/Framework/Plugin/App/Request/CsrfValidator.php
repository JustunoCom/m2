<?php
namespace Justuno\Core\Framework\Plugin\App\Request;
use Justuno\Core\Framework\Action as DfA;
use Magento\Framework\App\ActionInterface as IA;
use Magento\Framework\App\Request\CsrfValidator as Sb;
use Magento\Framework\App\Request\Http as R;
use Magento\Framework\App\RequestInterface as IR;
/**
 * 2021-02-23
 * 1) https://github.com/mage2pro/core/blob/7.1.7/Framework/Plugin/App/Request/CsrfValidator.php#L9-L38
 * 2) "Implement a database diagnostic tool": https://github.com/justuno-com/core/issues/347 -->
 */
final class CsrfValidator {
	/**
	 * 2020-02-25
	 * @see \Magento\Framework\App\Request\CsrfValidator::validate():
	 *		try {
	 *			$areaCode = $this->appState->getAreaCode();
	 *		}
	 * 		catch (LocalizedException $exception) {
	 *			$areaCode = null;
	 *		}
	 *		if ($request instanceof HttpRequest && in_array($areaCode, [Area::AREA_FRONTEND, Area::AREA_ADMINHTML], true)) {
	 *			$valid = $this->validateRequest($request, $action);
	 *			if (!$valid) {
	 *				throw $this->createException($request, $action);
	 *			}
	 *		}
	 * https://github.com/magento/magento2/blob/2.3.4/lib/internal/Magento/Framework/App/Request/CsrfValidator.php#L111-L135
	 * @param Sb $sb
	 * @param \Closure $f
	 * @param IR|R $r
	 * @param IA $a
	 * @return bool
	 */
	function aroundValidate(Sb $sb, \Closure $f, IR $r, IA $a) {return $a instanceof DfA || $f($r, $a);}
}