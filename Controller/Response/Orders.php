<?php
namespace Justuno\M2\Controller\Response;
class Orders extends \Magento\Framework\App\Action\Action {

	protected $_pageFactory;
	protected $request;
	protected $_customerFactory;
	protected $_addressFactory;

	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Sales\Model\Order $order,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
		\Magento\Customer\Model\AddressFactory $addressFactory
		)
	{
		$this->_storeManager    = $storeManager;
		$this->request          = $request;
		$this->_order           = $order;
		$this->_customerFactory = $customerFactory;
		$this->_addressFactory  = $addressFactory;
		return parent::__construct($context);
	}

	function execute()
	{

			$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
			$storeUrl       = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
			header('Content-Type: application/json');
			foreach (getallheaders() as $name => $value) {
				if($name == "Authorization") {
					$req_token = $value; break;
				} else {
					$req_token = false;
				}
			}
			if($req_token == null || $req_token == ''){
				$response = array(
					'response' => false,
					'message'  => 'Authorization Failed'
				);
				echo json_encode($response);
				return false;
			}
			$apitoken = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('justuno_settings/options_interface/token_key','stores');

			if($req_token == $apitoken){ // validate token

				$apiURL     = $storeUrl . "index.php/rest/V1/integration/admin/token";

				$parameters      = array(
					'sortOrders'  => $this->request->getParam('sortOrders'),
					'pageSize'    => $this->request->getParam('pageSize'),
					'currentPage' => $this->request->getParam('currentPage'),
					'filterBy'    => $this->request->getParam('filterBy')
				);

				$queryUrl = $this->build_http_query( $parameters );
				$data = array("username" => "justunouser", "password" => "hello@123");
				$data_string = json_encode($data);
				$ch = curl_init($apiURL);

				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Content-Length: ".strlen($data_string)));

				$token = curl_exec($ch);
				$token=  json_decode($token);
				$headers = array("Authorization: Bearer ".$token, "Content-Type: application/json");

				$requestUrl= $storeUrl . 'index.php/rest/V1/orders?'.$queryUrl;
				$ch = curl_init($requestUrl);

				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				$result  = curl_exec($ch);
				$results = json_decode($result);
				if( $results->search_criteria->current_page != TRUE ){
					$response = array(
						'response' => FALSE,
						'message'  => 'Page not found'
					);
					echo json_encode( $response ); exit('');
				}

				$formattedJson = $lineItems = array();
				if( !empty($results ) && isset($results->total_count) ) {

					if ($results->total_count < ($results->search_criteria->page_size * ($results->search_criteria->current_page - 1) ) ) {
						$response = array(
						'response' => NULL,
						'message'  => 'No data found'
					);
					echo json_encode( $response ); exit('');
					}

					$TotalRecords = $results->total_count;

						foreach($results->items as $result) {
							$uid = uniqid();
							if(isset($result->customer_id  ) && !empty($result->customer_id ) ) {
								$cust_id    = $result->customer_id;
								$userData   =  $this->customerData($cust_id); /** Get customer data **/
							} else {

								$userData = array(
									'id' => $uid,
									'CreatedAt'     => $result->created_at,
									'UpdatedAt'     => $result->updated_at,
									'TotalSpend'    => $result->grand_total
								);
							}


							$items = $result->items;    /** Get order items */
							$TotalItems = count($items);
							if(!empty($items) ){

								foreach($items as $item) {
									$lineItems['LineItems'][] = array(
										'ProductId'   => "$item->product_id",
										'OrderId'     => "x",
										'VariantId'   => '',
										'Price'       => $item->price,
										'TotalDiscount'=> $item->discount_amount
									);
								}
							}

							if(isset($result->customer_id )){
								$id = $result->customer_id;
							} else {
								$id = $uid;
							}
							if(isset($result->remote_ip)) {
								$ip = $result->remote_ip;
							} else{
								$ip = '';
							}
							$cntry = $result->billing_address->country_id;
							$formattedJson[] = array_merge(
								array(
									'id'          => $result->increment_id,
									'OrderNumber' => "$result->entity_id",
									'CustomerId'  => "$id",
									'Email'       => $result->customer_email,
									'CreatedAt'     => $result->created_at,
									'UpdatedAt'     => $result->updated_at,
									'TotalPrice'    => $result->grand_total,
									'SubtotalPrice' => $result->subtotal,
									'ShippingPrice' => $result->shipping_amount,
									'TotalTax'      => $result->tax_amount,
									'TotalDiscounts'=> $result->base_discount_amount,
									'TotalItems'  => $TotalItems,
									'Currency'    => $result->order_currency_code,
									'Status'      => $result->status,
									'CountryCode' => $cntry,
									'IP'          => $ip,
									'TotalRecords'  => "$TotalRecords",
									'Customer'     => $userData
								), $lineItems);
						unset($lineItems);
					}
				}
				$array     = array_map('array_filter', $formattedJson);
				$finalData = array_filter($array);

				// echo "<pre>"; print_r($formattedJson); die('asd');
				echo json_encode($finalData,  JSON_PRETTY_PRINT); exit();
			}  else {
				$response = array(
					'response' => false,
					'message'  => 'Please provide a valid token key'
				);
				echo json_encode($response);
			}

	}




	/**generate query parameters */
	function build_http_query( $query ){
		$query_array = array();
		foreach( $query as $key => $key_value ){
			if($key_value == ''){continue;}
			if( $key == 'sortOrders' ) {
				$query_array[]  = "searchCriteria[$key][0][field]=".urlencode( $key_value );
			} else if($key == "filterBy"){
				$todate =  urlencode( $key_value );
				$query_array[] = "searchCriteria[filter_groups][0][filters][0][field]=updated_at&searchCriteria[filter_groups][0][filters][0][value]=$todate&searchCriteria[filter_groups][0][filters][0][condition_type]=gteq";
			} else {
				$query_array[] = "searchCriteria[$key]=" .urlencode( $key_value );
			}
		}
		return implode( '&', $query_array );
	}

	function customerData($customerId) {

		$customer    = $this->_customerFactory->create()->load($customerId);

		$orderData   =   $this->getOrders($customerId);
		$OrdersCount = $orderData['orderCount'];
		$totalSpent  = $orderData['totalSpend'];

		$billingAddressId = $customer->getDefaultBilling();

		$addressData = $this->_addressFactory->create()->load($billingAddressId);

		$address     = $addressData->getStreet();


		if(!empty($address['0'])) {
			$address1 = $address['0'];
		} else {
			$address1 = '';
		}
		if(!empty($address['1'])) {
			$address2 = $address['1'];
		} else {
			$address2 = '';
		}


		$formattedJson = array(
			'id'        => $customer->getEntityId(),
			'email'     => $customer->getEmail(),
			'CreatedAt' => $customer->getCreatedAt(),
			'UpdatedAt' => $customer->getUpdatedAt(),
			'FirstName' => $customer->getFirstname(),
			'LastName'  => $customer->getLastname(),
			'OrdersCount' => $OrdersCount,
			'TotalSpend'  => $totalSpent,
			'Tags'        => '',
			'address1'    => $address1,
			'address2'    => $address2,
			'City'        => $addressData->getCity(),
			'Zip'         => $addressData->getPostcode(),
			'ProvinceCode'=> $addressData->getRegion(),
			'CountryCode' => $addressData->getCountryId()
		);


		return $formattedJson;

	}


	function getOrders($customerId) {

		$orderCollection = $this->_order->getCollection()->addAttributeToFilter('customer_id', $customerId);
		$orders     = $orderCollection->getData();
		$data       = array();
		$orderCount = $orderCollection->getSize();

		if($orderCount > 0 ){
			$totalSpend = 0;

			foreach($orders as $order ){
				$totalSpend+= $order['grand_total'];
			}
			$data['orderCount'] = $orderCount;
			$data['totalSpend'] = $totalSpend;

		} else {
			$data = array(
				'orderCount' => 0,
				'totalSpend' => 0
			);
		}
		return $data;
	}

}
