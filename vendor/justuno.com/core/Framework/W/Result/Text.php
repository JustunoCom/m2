<?php
namespace Justuno\Core\Framework\W\Result;
use Magento\Framework\App\Response\Http as HttpResponse;
use Magento\Framework\App\Response\HttpInterface as IHttpResponse;
/**
 * 2016-07-04
 * 2016-08-24
 * Дибилоид Vinai Kopp 2016-05-12 внёс такой коммит: https://github.com/magento/magento2/commit/c930932
 * Его включили в 2.2-dev 2016-08-10: https://github.com/magento/magento2/blob/c930932/lib/internal/Magento/Framework/Controller/Result/Json.php#L64
 * Этот коммит ломает совместимость сигнатуры метода @see \Magento\Framework\Controller\Result\Json::render()
 * с более ранними версиями.
 *
 * Раньше было:
 * protected function render(ResponseInterface $response)
 * https://github.com/magento/magento2/blob/2.1.0/lib/internal/Magento/Framework/Controller/Result/Json.php#L64
 *
 * Теперь стало:
 * protected function render(HttpResponseInterface $response)
 *
 * Поэтому мы не можем универсально перекрыть метод render (чтобы это работало сразу во всех версиях):
 * Fatal error: Declaration of Justuno\Core\Framework\W\Result\Json::render()
 * must be compatible with Magento\Framework\Controller\AbstractResult::render
 * (Magento\Framework\App\Response\HttpInterface $response)
 * in C:\work\mage2.pro\store\vendor\mage2pro\core\Framework\Controller\Result\Json.php on line 5
 *
 * Поэтому вместо наследования от @see \Magento\Framework\Controller\Result\Json
 * просто копируем его реализацию в класс @see \Df\Framework\W\Result\JsonM.
 *
 * 2020-08-23 "Port the `Df\Framework\W\Result\Text` class" https://github.com/justuno-com/core/issues/271
 * @see \Justuno\Core\Framework\W\Result\Json
 */
class Text extends \Justuno\Core\Framework\W\Result {
	/**
	 * 2017-03-30
	 * 2017-11-17
	 * We can use the PHP «final» keyword here,
	 * because the method is absent in @see \Magento\Framework\Controller\ResultInterface
	 * @override
	 * @see \Justuno\Core\Framework\W\Result::__toString()
	 * @return string
	 */
	final function __toString() {return $this->_body;}

	/**
	 * 2016-08-24
	 * @used-by render()
	 * @see \Justuno\Core\Framework\W\Result\Json::contentType()
	 * @return string
	 */
	protected function contentType() {return 'text/plain';}

	/**
	 * 2016-08-24
	 * @used-by i()
	 * @see \Justuno\Core\Framework\W\Result\Json::prepare()
	 * @param mixed $body
	 * @return string
	 */
	protected function prepare($body) {return $body;}

	/**
	 * 2016-07-04
	 * @override
	 * @see \Justuno\Core\Framework\W\Result::render()
	 * @used-by \Justuno\Core\Framework\W\Result::renderResult()
	 * @param IHttpResponse|HttpResponse $r
	 */
	final protected function render(IHttpResponse $r) {
		$r->setBody($this->_body);
		ju_response_content_type(implode('; ', [$this->contentType(), 'charset=utf-8']), $r);
	}

	/**
	 * 2016-07-04
	 * @used-by __toString()
	 * @used-by i()
	 * @used-by render()
	 * @var string
	 */
	private $_body;

	/**
	 * 2016-07-04
	 * @used-by \Justuno\M2\Response::p()
	 * @param mixed $body
	 * @return self
	 */
	static function i($body) {
		$r = new static; /** @var self $r */
		$r->_body = $r->prepare($body);
		return $r;
	}
}