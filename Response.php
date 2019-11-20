<?php
namespace Justuno\M2;
use Df\Framework\W\Result\Json;
// 2019-10-30
final class Response {
	/**
	 * 2019-11-20
	 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
	 * @used-by \Justuno\M2\Controller\Response\Orders::execute()
	 * @param \Closure $f
	 * @return Json
	 */
	static function p(\Closure $f) {/** @var array(string => mixed) $r */
		try {
			if (!df_my_local()
				&& df_request_header('Authorization') !== df_cfg('justuno_settings/options_interface/token_key')
			) {
				df_error('Please provide a valid token key');
			}
			$r = $f();
		}
		catch (\Exception $e) {
			$r = ['message' => $e->getMessage()];
		}
		return Json::i(self::filter($r));
	}

	/**
	 * 2019-10-30
	 * «if a property is null or an empty string do not send it back»: https://github.com/justuno-com/m1/issues/9
	 * @used-by filter()
	 * @used-by res()
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