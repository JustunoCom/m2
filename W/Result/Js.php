<?php
namespace Justuno\M2\W\Result;
use Magento\Framework\App\Response\Http as R;
use Magento\Framework\App\Response\HttpInterface as IR;
/**
 * 2020-03-14
 * "Respond to the `/justuno/service-worker.js` request with the provided JavaScript":
 * https://github.com/justuno-com/m2/issues/10
 * 2020-03-15
 * "Replace the `/justuno/service-worker.js` URL with  `/apps/justuno/service-worker.js`":
 * https://github.com/justuno-com/m2/issues/11
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 */
class Js extends \Justuno\Core\Framework\W\Result {
	/**
	 * 2020-03-14
	 * We can use the PHP «final» keyword here,
	 * because the method is absent in @see \Magento\Framework\Controller\ResultInterface
	 * @override
	 * @see \Justuno\Core\Framework\W\Result::__toString()
	 * @used-by self::render()
	 */
	final function __toString():string {return $this->_r;}

	/**
	 * 2020-03-14
	 * @override
	 * @see \Justuno\Core\Framework\W\Result::render()
	 * @used-by \Justuno\Core\Framework\W\Result::renderResult()
	 * @param IR|R $r
	 */
	final protected function render(IR $r):void {
		$r->setBody($this->__toString());
		ju_response_content_type('application/javascript', $r);
	}

	/**
	 * 2020-03-14
	 * @used-by self::__toString()
	 * @used-by self::i()
	 * @var string
	 */
	private $_r;

	/**
	 * 2020-03-14
	 * @used-by \Justuno\M2\Controller\Js::execute()
	 */
	final static function i(string $name):self {
		$i = new self; /** @var self $i */
		$i->_r = ju_module_file(__CLASS__, "js/$name", 'js', true, function($f) {return file_get_contents($f);});
		return $i;
	}
}