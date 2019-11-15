<?php
namespace Justuno\M2\Block;
use Magento\Framework\View\Element\AbstractBlock as _P;
// 2019-11-15
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Js extends _P {
	/**
	 * 2019-11-15
	 * @override
	 * @see _P::_toHtml()
	 * @used-by _P::toHtml():
	 *		$html = $this->_loadCache();
	 *		if ($html === false) {
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->suspend($this->getData('translate_inline'));
	 *			}
	 *			$this->_beforeToHtml();
	 *			$html = $this->_toHtml();
	 *			$this->_saveCache($html);
	 *			if ($this->hasData('translate_inline')) {
	 *				$this->inlineTranslation->resume();
	 *			}
	 *		}
	 *		$html = $this->_afterToHtml($html);
	 * https://github.com/magento/magento2/blob/2.2.0/lib/internal/Magento/Framework/View/Element/AbstractBlock.php#L643-L689
	 * @return string
	 */
	final protected function _toHtml() {return
		!df_is_guid($id = df_cfg('justuno_settings/options_interface/accid')) ? '' : df_js(__CLASS__, '', [
			'merchantId' => $id
		] + (!df_is_catalog_product_view() ? [] : [
			'action' => df_action_name(), 'productId' => df_product_current_id()			
		]))
	;}
}