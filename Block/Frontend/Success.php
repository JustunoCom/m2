<?php
namespace Justuno\Jumagext\Block\Frontend;
use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\OrderRepositoryInterface;
class Success extends Template {

	/**
	 * @var OrderRepositoryInterface
	 */
	protected $orderRepository;


	/**
	 * @var Session
	 */
	protected $checkoutSession;

	/**
	 * @var Template\Context
	 */
	protected $context;

	/**
	 * @var StoreManager
	 */
	protected $_storeManager;


	/**
	 * MagexSync constructor.
	 * @param OrderRepositoryInterface   $orderRepository
	 * @param Session   $checkoutSession
	 * @param Template\Context $context
	 * @param array            $data
	 */
	function __construct(
		OrderRepositoryInterface $orderRepository,
		Session $checkoutSession,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		Template\Context $context,
		array $data
	) {
		parent::__construct($context, $data);
		$this->checkoutSession = $checkoutSession;
		$this->orderRepository = $orderRepository;
		$this->_storeManager = $storeManager;
	}


	/**
	 * @return string
	 */
	function juGetOrderId()
	{
		$lastorderId = $this->checkoutSession->getLastOrderId();
		return $lastorderId;
	}

	/**
	 * @return string
	 */
	function juGetOrderById($id) {
		return $this->orderRepository->get($id);
	}

	/**
	* Get current store currency code
	*
	* @return string
	*/
	function getCurrentCurrencyCode()
	{
		return $this->_storeManager->getStore()->getCurrentCurrencyCode();
	}
}

