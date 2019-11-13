<?php
namespace Justuno\Jumagext\Block\Frontend;
use Justuno\Jumagext\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
/**
 * 2019-11-13
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 * @used-by view/frontend/layout/default.xml
 */
class Embed extends Template {
	/**
	 * 2019-11-13
	 * @param Data $helper
	 * @param Http $request
	 * @param Registry $registry
	 * @param Session $customerSession
	 * @param array $data
	 * @param Template\Context $context
	 */
	function __construct(
		Data $helper, Http $request
		,Session $customerSession
		,Registry $registry
		,Template\Context $context
		,array $data = []
	) {
		$this->_request = $request;
		$this->customerSession = $customerSession;
		$this->data = $data;
		$this->helper = $helper;
		$this->registry = $registry;
		parent::__construct($context, $data);
	}

	/**
	 * 2019-11-13
	 * @return boolean
	 */
	function customerLoggedIn() {return $this->customerSession->isLoggedIn();}

	/**
	 * 2019-11-13
	 * @return object
	 */
	function getCurrentProductID() {return $this->registry->registry('current_product')->getID();}

	/**
	 * 2019-11-13
	 * @return string
	 */
	function getCustomerID() {return $this->customerSession->getCustomer()->getId();}

	/**
	 * 2019-11-13
	 * @return string
	 */
	function getFullActionName() {return $this->_request->getFullActionName();}

	/**
	 * 2019-11-13
	 * @used-by view/frontend/templates/embed.phtml
	 * @return string
	 */
	function getValueACCID() {return $this->helper->getACCID();}

	/**
	 * 2019-11-13
	 * @var Session
	 */
	private $customerSession;
	/**
	 * 2019-11-13
	 * @var Data
	 */
	private $helper;
	/**
	 * 2019-11-13
	 * @var Registry
	 */
	private $registry;
	/**
	 * 2019-11-13
	 * @var Http
	 */
	private $_request;
}