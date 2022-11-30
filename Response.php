<?php
namespace Justuno\M2;
use Justuno\Core\Framework\W\Result\Json;
# 2019-10-30
final class Response {
	/**
	 * 2019-11-20
	 * @used-by \Justuno\M2\Controller\Cart\Add::execute()
	 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
	 * @used-by \Justuno\M2\Controller\Response\Inventory::execute()
	 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
	 */
	static function p(\Closure $f):Json {/** @var array(string => mixed) $r */
		try {
			$r = $f();
			ju_sentry(__CLASS__, sprintf('%s: %s', ju_request_o()->getHttpHost(), ju_class_l(ju_caller_c())));
		}
		catch (\Exception $e) {
			$r = ['message' => $e->getMessage()];
			ju_sentry(__CLASS__, $e);
		}
		return Json::i(is_null($r) ? 'OK' : (!is_array($r) ? $r : self::filter($r)));
	}

	/**
	 * 2019-10-30 «if a property is null or an empty string do not send it back»: https://github.com/justuno-com/m1/issues/9
	 * @used-by filter()
	 * @used-by p()
	 * @param array(string => mixed) $a
	 * @return array(string => mixed)
	 */
	private static function filter(array $a) {
		$r = []; /** @var array(string => mixed) $r */
		foreach ($a as $k => $v) { /** @var string $k */ /** @var mixed $v */
			if (!in_array($v, ['', null], true)) {
				$r[$k] = !is_array($v) ? $v : self::filter($v);
			}
		}
		return $r;
	}
}