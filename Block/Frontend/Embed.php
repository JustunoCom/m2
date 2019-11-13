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
 * @used-by vendor/justuno.com/m2/view/frontend/layout/default.xml
 */
class Embed extends Template {
	/**
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
	 * @return boolean
	 */
	function customerLoggedIn() {return $this->customerSession->isLoggedIn();}

	/**
	 * @return object
	 */
	function getCurrentProductID() {return $this->registry->registry('current_product')->getID();}

	/**
	 * @return string
	 */
	function getCustomerID() {return $this->customerSession->getCustomer()->getId();}

	/**
	 * @return string
	 */
	function getFullActionName() {return $this->_request->getFullActionName();}

	/**
	 * @return string
	 */
	function getValueACCID() {return $this->helper->getACCID();}

	/**
	 * @return string
	 */
	function getValueJUAJAX() {return $this->helper->getJUAJAX();}

	/**
	 * @var Http
	 */
	protected $_request;
	/**
	 * @var Session
	 */
	protected $customerSession;
	/**
	 * @var Registry
	 */
	protected $registry;
	/**
	 * @var Data
	 */
	private $helper;
}