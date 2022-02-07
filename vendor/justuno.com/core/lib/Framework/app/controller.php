<?php
use Justuno\Core\Framework\W\Result as wResult;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\Response\HttpInterface as IHttpResponse;
use Magento\Framework\App\ResponseInterface as IResponse;
use Magento\Framework\Controller\ResultInterface as IResult;
/**
 * 2021-02-26
 * 2021-08-05 @deprecated It is unused.
 */
function ju_403() {ju_response_code(403);}

/**
 * 2017-02-01
 * 2017-11-17
 * You can read here more about the IResult/wResult and IResponse/HttpResponse difference:
 * 1) @see \Magento\Framework\App\Http::launch():
 *		# TODO: Temporary solution until all controllers return ResultInterface (MAGETWO-28359)
 *		if ($result instanceof ResultInterface) {
 *			$this->registry->register('use_page_cache_plugin', true, true);
 *			$result->renderResult($this->_response);
 *		} elseif ($result instanceof HttpInterface) {
 *			$this->_response = $result;
 *		} else {
 *			throw new \InvalidArgumentException('Invalid return type');
 *		}
 * https://github.com/magento/magento2/blob/2.2.1/lib/internal/Magento/Framework/App/Http.php#L122-L149
 * 2) "[Question] To ResultInterface or not ResultInterface": https://github.com/magento/magento2/issues/1355
 * https://github.com/magento/magento2/issues/1355
 * 2020-08-21 "Port the `ju_response` function" https://github.com/justuno-com/core/issues/235
 * @used-by ju_response_code()
 * @used-by ju_response_content_type()
 * @param IResult|wResult|IResponse|HttpResponse|null $r [optional]
 * @return IResponse|IHttpResponse|HttpResponse|IResult|wResult
 */
function ju_response($r = null) {return $r ?: ju_o(IResponse::class);}

/**
 * 2015-11-29
 * @used-by ju_403()
 * @param int $v
 */
function ju_response_code($v) {ju_response()->setHttpResponseCode($v);}

/**
 * I pass the 3rd argument ($replace = true) to @uses \Magento\Framework\HTTP\PhpEnvironment\Response::setHeader()
 * because the `Content-Type` headed can be already set.
 * 2020-08-21 "Port the `df_response_content_type` function" https://github.com/justuno-com/core/issues/234
 * @used-by \Justuno\Core\Framework\W\Result\Text::render()
 * @used-by \Justuno\M2\W\Result\Js::render()
 * @param string $contentType
 * @param IResult|wResult|IHttpResponse|HttpResponse|null $r [optional]
 */
function ju_response_content_type($contentType, $r = null) {ju_response($r)->setHeader('Content-Type', $contentType, true);}