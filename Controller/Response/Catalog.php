<?php
namespace Justuno\M2\Controller\Response;
use Df\Framework\W\Result\Json;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\Action\Action as _P;
use Magento\Review\Model\ResourceModel\Review\Collection as RC;
// 2019-11-17
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class Catalog extends _P {
	/**
	 * 2019-11-17
	 * @override
	 * @see _P::execute()
	 * @used-by \Magento\Framework\App\Action\Action::dispatch():
	 * 		$result = $this->execute();
	 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Action/Action.php#L84-L125
	 * @return Json
	 */
	function execute() {
		try {
			$r = []; /** @var array(string => mixed) $r */
			if (!df_my_local()
				&& df_request_header('Authorization') !== df_cfg('justuno_settings/options_interface/token_key')
			) {
				df_error('Please provide a valid token key');
			}
			$storeUrl = df_store()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
			$mediaUrl = df_store()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
			$queryUrl = $this->build_http_query(df_request(['currentPage', 'filterBy', 'pageSize', 'sortOrders']));
			$ch = curl_init("{$storeUrl}index.php/rest/V1/products?$queryUrl");
			curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer {$this->token()}", 'Content-Type: application/json']);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			$cdata = curl_exec($ch);
			$res = df_json_decode($cdata);
			$formattedJson  = $categoryData = $special_price = $prod_url = [];
			if ($m = dfa($res, 'message')) {
				df_error($m);
			}
			$sc = dfa($res, 'search_criteria', []); /** @var array(string => mixed) $sc */
			if (!($currentPage = (int)dfa($sc, 'current_page'))) {  /** @var int $currentPage */
				df_error('Page not found');
			}
			if (!($totalProducts = (int)dfa($res, 'total_count'))) { /** @var int $totalProducts */
				df_error('No data found');
			}
			$pageSize = (int)dfa($sc, 'page_size'); /** @var int $pageSize */
			if ($totalProducts < $pageSize * ($currentPage - 1)) {
				df_error('No data found');
			}
			foreach (dfa($res, 'items') as $item) {
				if ($caA = dfa($item, 'custom_attributes')) {
					$special_price = $brandName = null;
					foreach ($caA as $ca) {
						$c = dfa($ca, 'attribute_code'); /** @var string $c */
						$v = dfa($ca, 'value');
						if ('url_key' === $c) {
							$url = $v;
						}
						elseif ('special_price' === $c) {
							$special_price = (int)$v;
						}
						elseif ('country_of_manufacture' === $c) {
							$brandName = $v;
						}
					}
				}
				$rc = df_new_om(RC::class); /** @var RC $rc */
				$rc->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED);
				$rc->addEntityFilter('product', dfa($item, 'id'));
				$rc->setDateOrder();
				$mge = dfa($item, 'media_gallery_entries');
				$gallery_count = count($mge);
				if ($gallery_count > 0) {
					for ($k = 0; $k <= $gallery_count; $k++) {
						if ($file = dfa_deep($mge, "$k/file")) {
							$imgkey = $k+1;
							$image['ImageUrl'. $imgkey] = $mediaUrl . 'catalog/product' . $file;
						}
					}
				}
				$catDetails = array();
				if ($links = dfa_deep($item, 'extension_attributes/category_links')) {
					foreach ($links as $catlink) {
						$catData = df_category(dfa($catlink, 'category_id')); /** @var Category $catData */
						$categId = $catData->getId();
						$catDetails['ID']       = "$categId";
						$catDetails['Name']     = $catData->getName();
						$catDetails['Description'] = strip_tags($catData->getDescription());
						$catDetails['Keyword']  = $catData->getMetaKeywords();
						$catDetails['URL']      =  $catData->getUrl();
						$catDetails['ImageURL'] =
							!($img = $catData->getImage()) ? null : "{$mediaUrl}catalog/category/$img"
						;
						$categoryData[] = $catDetails;
					}
					unset($catData);
				}
				$Catarray = array_map('array_filter', $categoryData);
				$categoryData = array_filter($Catarray);
				$formattedJson[] = array_merge( array(
					'ID'        => dfa($item, 'sku'),
					'MSRP'      => $special_price,
					'Price'     => dfa($item, 'price'),
					'SalePrice' => dfa($item, 'price'),
					'Title'     => dfa($item, 'name'),
					'URL'         => $storeUrl.$url,
					'CreatedAt'   => dfa($item, 'created_at'),
					'UpdatedAt'   => dfa($item, 'updated_at'),
					'ReviewsCount' => $rc->getSize() ?: null,
					'ReviewsRatingSum' => '',
					'Categories'  => $categoryData,
					'BrandId'     => df_cfg('justuno_settings/options_interface/brand_attribute'),
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
		}
		catch (\Exception $e) {
			$r = ['message' => $e->getMessage(), 'response' => null];
		}
		return Json::i($r);
	}

	/**
	 * 2019-11-17
	 * @used-by execute()
	 * @param array(string => mixed) $query
	 * @return string
	 */
	private function build_http_query($query) {
		$query_array = [];
		foreach ($query as $key => $key_value) {
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

	/**
	 * 2019-11-18
	 * @return string
	 */
	private function token() {
		$storeUrl = df_store()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
		$apiURL = $storeUrl . "index.php/rest/V1/integration/admin/token";
		$ch = curl_init($apiURL);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post = json_encode([
			'password' => 'hello@123', 'username' => 'justunouser'
		]));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if (!($r = json_decode(curl_exec($ch)))) {  /** @var string $r */
			df_error("Unable to be authenticated as `justunouser`");
		}
		return $r;
	}
}