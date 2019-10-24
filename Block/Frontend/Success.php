<?php

namespace Justuno\Jumagext\Block\Frontend;

use Magento\Framework\View\Element\Template;
use Magento\Checkout\Model\Session;
use Magento\Sales\Api\OrderRepositoryInterface;

class Success extends Template
{

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
    public function __construct(
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
    public function juGetOrderId()
    {
        $lastorderId = $this->checkoutSession->getLastOrderId();
		return $lastorderId;
	}

	/**
     * @return string
     */
	public function juGetOrderById($id) {
        return $this->orderRepository->get($id);
    }

    /**
    * Get current store currency code
    *
    * @return string
    */
    public function getCurrentCurrencyCode()
    {
        return $this->_storeManager->getStore()->getCurrentCurrencyCode();
    }
}

