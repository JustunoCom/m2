<?php
namespace Justuno\Jumagext\Block\System\Config\Form;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Button extends \Magento\Config\Block\System\Config\Form\Field
{
	 const BUTTON_TEMPLATE = 'system/config/button/button.phtml';

	 /**
	 * Set template to itself
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		if (!$this->getTemplate()) {
			$this->setTemplate(static::BUTTON_TEMPLATE);
		}
		return $this;
	}
	/**
	 * Render button
	 *
	 * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
	 * @return string
	 */
	function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		// Remove scope label
		$element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
		return parent::render($element);
	}

	 /**
	 * Get the button and scripts contents
	 *
	 * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
	 * @return string
	 */
	protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
	{
		//$originalData = $element->getOriginalData();
		$this->addData(
			[
				'id'        => 'justuno_token_button',
				/**
				 * 2019-10-245 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
				 * «Call to undefined function Justuno\Jumagext\Block\System\Config\Form\_()
				 * in vendor/justuno.com/m2/Block/System/Config/Form/Button.php:48»:
				 * https://github.com/justuno-com/m2/issues/2
				 */
				'button_label' => __('Generate Token')
			]
		);
		return $this->_toHtml();
	}
}
