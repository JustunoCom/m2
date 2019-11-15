<?php
namespace Justuno\M2\Block\System\Config\Form;
use Magento\Framework\Data\Form\Element\AbstractElement as E;
// 2019-11-14
class Button extends \Magento\Config\Block\System\Config\Form\Field {
	/**
	 * 2019-11-14
	 * @param E $e
	 * @return string
	 */
	function render(E $e) {
		$e->unsetData(['can_use_default_value', 'can_use_website_value', 'scope']);
		return parent::render($e);
	}

	/**
	 * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
	 * @return string
	 */
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element) {
		$this->addData([
			'id' => 'justuno_token_button',
			/**
			 * 2019-10-245 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
			 * «Call to undefined function Justuno\M2\Block\System\Config\Form\_()
			 * in vendor/justuno.com/m2/Block/System/Config/Form/Button.php:48»:
			 * https://github.com/justuno-com/m2/issues/2
			 */
			'button_label' => __('Generate Token')
		]);
		return $this->_toHtml();
	}

	/**
	 * @return $this
	 */
	protected function _prepareLayout() {
		parent::_prepareLayout();
		$this->setTemplate('system/config/button/button.phtml');
		return $this;
	}
}