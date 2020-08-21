<?php
namespace Justuno\M2\Block;
use Justuno\Core\Framework\Form\ElementI;
use Magento\Backend\Block\Widget\Button as W;
use Magento\Framework\Data\Form\Element\AbstractElement as E;
# 2019-11-15
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class GenerateToken extends E implements ElementI {
	/**
	 * 2017-06-27
	 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
	 * @override
	 * @see \Magento\Framework\Data\Form\Element\AbstractElement::getElementHtml()
	 * @used-by \Magento\Framework\Data\Form\Element\AbstractElement::getDefaultHtml():
	 *		public function getDefaultHtml() {
	 *			$html = $this->getData('default_html');
	 *			if ($html === null) {
	 *				$html = $this->getNoSpan() === true ? '' : '<div class="admin__field">' . "\n";
	 *				$html .= $this->getLabelHtml();
	 *				$html .= $this->getElementHtml();
	 *				$html .= $this->getNoSpan() === true ? '' : '</div>' . "\n";
	 *			}
	 *			return $html;
	 *		}
	 * https://github.com/magento/magento2/blob/2.2.0-RC1.8/lib/internal/Magento/Framework/Data/Form/Element/AbstractElement.php#L426-L441
	 * @return string
	 */
	function getElementHtml() {return ju_block(W::class, ['id' => $this->getHtmlId(), 'label' => 'Generate Token'])->toHtml();}

	/**
	 * 2017-06-27
	 * 2017-06-28 Dynamics 365:
	 * «Request an authorization code -
	 * Authorize access to web applications using OAuth 2.0 and Azure Active Directory»
	 * https://docs.microsoft.com/en-us/azure/active-directory/develop/active-directory-protocols-oauth-code#request-an-authorization-code
	 * 2017-07-10 Salesforce:
	 * «Understanding the Web Server OAuth Authentication Flow - Force.com REST API Developer Guide»
	 * https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/intro_understanding_web_server_oauth_flow.htm#d15809e72
	 * @override
	 * @see \Justuno\Core\Framework\Form\ElementI::onFormInitialized()
	 * @used-by \Justuno\Core\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 */
	final function onFormInitialized() {
		/**
		 * 2017-06-27
		 * This code removes the «[store view]» sublabel, similar to
		 * @see \Magento\MediaStorage\Block\System\Config\System\Storage\Media\Synchronize::render()
		 */
		$this->unsetData(['can_use_default_value', 'can_use_website_value', 'scope']);
		ju_fe_init($this, __CLASS__);
	}
}