<?php
use Justuno\Core\Exception as DFE;
use Magento\Customer\Model\Customer as C;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\Data\Customer as DC;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order as O;

/**
 * 2016-04-05
 * How to get a customer by his ID? https://mage2.pro/t/1136
 * How to get a customer by his ID with the @uses \Magento\Customer\Model\CustomerRegistry::retrieve()?
 * https://mage2.pro/t/1137
 * How to get a customer by his ID with the @see \Magento\Customer\Api\CustomerRepositoryInterface::getById()?
 * https://mage2.pro/t/1138
 * 2017-06-14 The $throw argument is not used for now.
 * @used-by ju_customer()
 * @used-by ju_sentry_m()
 * @param string|int|DC|C|null $c [optional]
 * @param bool $throw [optional]
 * @return C|O|null|false
 * @throws NoSuchEntityException|DFE
 * 2020-08-14 "Port the `df_customer` function" https://github.com/justuno-com/core/issues/187
 */
function ju_customer($c = null, $throw = false) {return ju_try(function() use($c) {return
	/** @var int|string|null $id */
	/**
	 * 2016-08-22
	 * I do not use @see \Magento\Customer\Model\Session::getCustomer()
	 * because it does not use the customers repository, and loads a customer directly from the database.
	 */
	!$c ? (
		ju_customer_session()->isLoggedIn()
			? ju_customer(ju_customer_id())
			: ju_error('ju_customer(): the argument is null and the visitor is anonymous.')
		) : ($c instanceof C ? $c : (
		($id =
			$c instanceof O ? $c->getCustomerId() : (
			is_int($c) || is_string($c) ? $c : (
			$c instanceof DC ? $c->getId() : null)
		))
			? ju_customer_registry()->retrieve($id)
			: ju_error('ju_customer(): the argument of type %s is unrecognizable.', ju_type($c))
	))
;}, $throw);}

/**
 * 2016-12-04
 * 2020-08-14 "Port the `df_customer_id` function" https://github.com/justuno-com/core/issues/188
 * @used-by ju_customer()
 * @used-by \Justuno\Core\Customer\Plugin\Js\CustomerId::afterGetSectionData()
 * @param C|DC|int|null $c [optional]
 * @return int|null
 */
function ju_customer_id($c = null) {return !$c && !ju_is_backend() ? ju_customer_session()->getId() : (
	$c instanceof C || $c instanceof DC ? $c->getId() : $c
);}

/**
 * 2016-04-05
 * 2020-08-14 "Port the `df_customer_registry` function" https://github.com/justuno-com/core/issues/189
 * @used-by ju_customer()
 * @return CustomerRegistry
 */
function ju_customer_registry() {return ju_o(CustomerRegistry::class);}

/**
 * 2020-08-14 "Port the `df_customer_session` function" https://github.com/justuno-com/core/issues/182
 * @used-by ju_customer()
 * @used-by ju_customer_id()
 * @used-by ju_customer_session_id()
 * @return Session
 */
function ju_customer_session() {return ju_o(Session::class);}

/**
 * 2020-01-25
 * 2020-01-26
 * 1) The customer session ID is regenerated (changes) via the methods:
 * 1.1) @see \Magento\Customer\Model\Session::regenerateId()
 *		public function regenerateId() {
 *			parent::regenerateId();
 *			$this->_cleanHosts();
 *			return $this;
 *		}
 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Customer/Model/Session.php#L564-L574
 * 1.2) @see \Magento\Framework\Session\SessionManager::regenerateId()
 *		public function regenerateId() {
 *			if (headers_sent()) {
 *				return $this;
 *			}
 *			if ($this->isSessionExists()) {
 *				session_regenerate_id();
 *				$newSessionId = session_id();
 *				$_SESSION['new_session_id'] = $newSessionId;
 *				$_SESSION['destroyed'] = time();
 *				session_commit();
 *				$oldSession = $_SESSION;
 *				session_id($newSessionId);
 *				session_start();
 *				$_SESSION = $oldSession;
 *				unset($_SESSION['destroyed']);
 *				unset($_SESSION['new_session_id']);
 *			}
 *			else {
 *				session_start();
 *			}
 *			$this->storage->init(isset($_SESSION) ? $_SESSION : []);
 *			if ($this->sessionConfig->getUseCookies()) {
 *				$this->clearSubDomainSessionCookie();
 *			}
 * 			return $this;
 *		}
 * https://github.com/magento/magento2/blob/2.3.3/lib/internal/Magento/Framework/Session/SessionManager.php#L522-L566
 * 2) regenerateId() is called from the following methods:
 * 2.1) \Magento\Backend\Model\Auth\Session::processLogin()
 * 2.2) \Magento\Checkout\Controller\Index\Index::execute():
 *		if (!$this->isSecureRequest()) {
 *			$this->_customerSession->regenerateId();
 *		}
 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Checkout/Controller/Index/Index.php#L40-L44
 * 2.3) \Magento\Customer\Controller\Account\CreatePost::execute()
 * 2.4) \Magento\Customer\Model\Session::setCustomerAsLoggedIn()
 * 2.5) \Magento\Customer\Model\Session::setCustomerDataAsLoggedIn()
 * 2.6) \Magento\Customer\Model\Plugin\CustomerNotification::beforeDispatch():
 *		<type name="Magento\Framework\App\Action\AbstractAction">
 *			<plugin name="customerNotification" type="Magento\Customer\Model\Plugin\CustomerNotification"/>
 *		</type>
 *		public function beforeDispatch(AbstractAction $subject, RequestInterface $request) {
 *			$customerId = $this->session->getCustomerId();
 *			if ($this->state->getAreaCode() == Area::AREA_FRONTEND && $request->isPost()
 *				&& $this->notificationStorage->isExists(
 *					NotificationStorage::UPDATE_CUSTOMER_SESSION,
 *					$customerId
 *				)
 *			) {
 *				try {
 *					$this->session->regenerateId();
 *					$customer = $this->customerRepository->getById($customerId);
 *					$this->session->setCustomerData($customer);
 *					$this->session->setCustomerGroupId($customer->getGroupId());
 *					$this->notificationStorage->remove(NotificationStorage::UPDATE_CUSTOMER_SESSION, $customer->getId());
 *				} catch (NoSuchEntityException $e) {
 *					$this->logger->error($e);
 *				}
 *			}
 *		}
 * https://github.com/magento/magento2/blob/2.3.3/app/code/Magento/Customer/Model/Plugin/CustomerNotification.php#L73-L101
 * 2020-08-14 "Port the `df_customer_session_id` function" https://github.com/justuno-com/core/issues/180
 * @used-by ju_is_frontend()
 * @used-by ju_sentry_m()
 * @return string|null
 */
function ju_customer_session_id() {return ju_etn(ju_customer_session()->getSessionId());}