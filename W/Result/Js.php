<?php
namespace Justuno\M2\W\Result;
use Magento\Framework\App\Response\Http as R;
use Magento\Framework\App\Response\HttpInterface as IR;
/**
 * 2020-03-14
 * "Respond to the `/justuno/service-worker.js` request with the provided JavaScript":
 * https://github.com/justuno-com/m2/issues/10
 * @final Unable to use the PHP «final» keyword here because of the M2 code generation.
 */
class Js extends \Justuno\Core\Framework\W\Result {
	/**
	 * 2020-03-14
	 * We can use the PHP «final» keyword here,
	 * because the method is absent in @see \Magento\Framework\Controller\ResultInterface
	 * @override
	 * @see \Df\Framework\W\Result::__toString()
	 * @used-by render()
	 * @used-by \Df\Payment\W\Action::execute()
	 * @return string
	 */
	final function __toString() {return $this->_r;}

	/**
	 * 2020-03-14
	 * @override
	 * @see \Df\Framework\W\Result::render()
	 * @used-by \Df\Framework\W\Result::renderResult()
	 * @param IR|R $r
	 */
	final protected function render(IR $r) {
		$r->setBody($this->__toString());
		df_response_content_type('application/javascript', $r);
	}

	/**
	 * 2020-03-14
	 * @used-by __toString()
	 * @used-by i()
	 * @var string
	 */
	private $_r;

	/**
	 * 2020-03-14
	 * $m could be:
	 * 1) A module name: «A_B`»
	 * 2) A class name: «A\B\C».
	 * 3) An object.
	 * @used-by \Justuno\M2\Controller\Js::execute()
	 * @param string|object $m
	 * @param string $name
	 * @return self
	 */
	final static function i($name) {
		$i = new self; /** @var self $i */
		$i->_r = df_module_file(__CLASS__, "js/$name", 'js', true, function($f) {return file_get_contents($f);});
		return $i;
	}
}