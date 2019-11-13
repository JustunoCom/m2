<?php

namespace Justuno\Jumagext\Controller\Response;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\RequestInterface;
use Magento\Review\Model\ReviewFactory;
use Magento\Review\Model\RatingFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Catalog extends \Magento\Framework\App\Action\Action
{
	protected $eavConfig;
	protected $_pageFactory;
	protected $_reviewFactory;
	protected $_ratingFactory;
	protected $request;

	function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\Category $category,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Review\Model\ReviewFactory $reviewFactory,
		\Magento\Review\Model\RatingFactory $ratingFactory,
		\Magento\Eav\Model\Config $eavConfig,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory     = $pageFactory;
		$this->_storeManager    = $storeManager;
		$this->_category        = $category;
		$this->request          = $request;
		$this->_reviewFactory   = $reviewFactory;
		$this->_ratingFactory   = $ratingFactory;
		$this->eavConfig        = $eavConfig;

		return parent::__construct($context);
	}

	// http://justuno.urtestsite.com/index.php/rest/V1/products?sortOrders=created_at&pageSize=10&currentPage=1

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


	function execute()
	{
		ini_set("display_errors", 1);
		$storeUrl   = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
		$mediaUrl   = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$apiURL     = $storeUrl . "index.php/rest/V1/integration/admin/token";

		header('Content-Type: application/json');
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		foreach (getallheaders() as $name => $value) {
			if($name == "Authorization") {
				$req_token = $value; break;
			} else {
				$req_token = false;
			}
		}
		if($req_token == null || $req_token == ''){
			$response = array (
				'response' => NULL,
				'message'  => 'Authorization Failed'
			);
			echo json_encode($response);
			return false;
		}


		$parameters     = array(
			'sortOrders'  => $this->request->getParam('sortOrders'),
			'pageSize'    => $this->request->getParam('pageSize'),
			'currentPage' => $this->request->getParam('currentPage'),
			'filterBy'    => $this->request->getParam('filterBy')
		);
		$queryUrl = $this->build_http_query( $parameters );
		$brandId = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('justuno_settings/options_interface/brand_attribute','stores');
		$apitoken = $objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface')->getValue('justuno_settings/options_interface/token_key','stores');

		if($req_token == $apitoken){ // valid token

			$data = array("username" => "justunouser", "password" => "hello@123");
			$data_string = json_encode($data);
			$ch = curl_init($apiURL);

			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Content-Length: ".strlen($data_string)));

			$token_result = curl_exec($ch);
			$token = json_decode($token_result);

			$headers = array("Authorization: Bearer ".$token, "Content-Type: application/json");

			$requestUrl= $storeUrl . 'index.php/rest/V1/products?'.$queryUrl;

			// echo $requestUrl;  die('test ');

			$ch = curl_init($requestUrl);

			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

			$cdata      = curl_exec($ch);
			$results    = json_decode($cdata);

			$formattedJson  = $categoryData = $special_price = $prod_url = array();

			 //echo "<pre>"; print_r($results); //die('asdat');
			 if( $results->search_criteria->current_page != TRUE ){
				$response = array(
					'response' => FALSE,
					'message'  => 'Page not found'
				);
				echo json_encode( $response ); exit('');
			}

			if( !empty($results && isset($results->total_count)) ) {
				if ($results->total_count < ($results->search_criteria->page_size * ($results->search_criteria->current_page - 1) ) ) {
					$response = array(
						'response' => NULL,
						'message'  => 'No data found'
					);
					echo json_encode( $response ); exit('');
				}
				$totalProducts = $results->total_count;
				foreach($results->items as $result) {

					if(!empty ($result->custom_attributes ) ){
						$special_price = $brandName = NULL;
						foreach($result->custom_attributes as $data) {
							if($data->attribute_code == 'url_key') {
								$url = $data->value;
							}
							if($data->attribute_code == 'special_price'){
								$special_price = (int)$data->value;
							}
							if($data->attribute_code == 'country_of_manufacture'){
								$brandName = $data->value;
							}
						}
					}

					$rating = $objectManager->get("Magento\Review\Model\ResourceModel\Review\CollectionFactory");
					$collection = $rating->create()
						->addStatusFilter(
							\Magento\Review\Model\Review::STATUS_APPROVED
						)->addEntityFilter(
							'product',
							$result->id
						)->setDateOrder();
					if($collection->getSize() ){
						$reviewCount = $collection->getSize();
					} else {
						$reviewCount = NULL;
					}

					$gallery_count = count($result->media_gallery_entries);
					if($gallery_count > 0 ){
						for($k = 0; $k <= $gallery_count; $k++) {
							if(isset($result->media_gallery_entries[$k]->file) ) {
								$imgkey = $k+1;
								$image['ImageUrl'. $imgkey] = $mediaUrl . 'catalog/product' . $result->media_gallery_entries[$k]->file;
							}
						}
					}

					$catDetails = array();
					if(!empty($result->extension_attributes->category_links) ){
						foreach($result->extension_attributes->category_links as $catlink){
							$catData = $this->_category->load($catlink->category_id);

							if(!empty($catData->getImage())) {
								$catimg = $mediaUrl . 'catalog/category/' .$catData->getImage();
							}
							$categId = $catData->getId();
							$catDetails['ID']       = "$categId";
							$catDetails['Name']     = $catData->getName();
							$catDetails['Description'] = strip_tags($catData->getDescription());
							$catDetails['Keyword']  = $catData->getMetaKeywords();
							$catDetails['URL']      =  $catData->getUrl();
							$catDetails['ImageURL'] = $catimg;
							$categoryData[] = $catDetails;
						}
						unset($catData);
					}
					$Catarray = array_map('array_filter', $categoryData);
					$categoryData = array_filter($Catarray);
					$formattedJson[] = array_merge( array(
						'ID'        => $result->sku,
						'MSRP'      => $special_price,
						'Price'     => $result->price,
						'SalePrice' => $result->price,
						'Title'     => $result->name,
						'URL'         => $storeUrl.$url,
						'CreatedAt'   => $result->created_at,
						'UpdatedAt'   => $result->updated_at,
						'ReviewsCount' => $reviewCount,
						'ReviewsRatingSum' => '',
						'Categories'  => $categoryData,
						'BrandId'     => $brandId,
						'BrandName'   => $brandName,
						'TotalRecords' => "$totalProducts"
					), $image);

					unset($special_price);
					unset($catDetails);
					unset($categoryData);



				}
				$array = array_map('array_filter', $formattedJson);
				$finalData = array_filter($array);

				echo json_encode( $finalData,  JSON_PRETTY_PRINT);
				exit();

			} else {
				$response = array(
					'response' => NULL,
					'message'  => 'No data found'
				);
				echo json_encode( $response ); exit('');
			}
		} else {
			$response = array(
				'response' => NULL,
				'message'  => 'Please provide a valid token key'
			);
			echo json_encode($response);
		}



	}




}