<?php
namespace Justuno\Core\Qa;
# 2020-06-18 "Port the `Df\Qa\Dumper` class": https://github.com/justuno-com/core/issues/82
final class Dumper {
	/**
	 * @used-by ju_dump()
	 * @used-by dumpArrayElements()
	 * @param mixed $v
	 * @return string
	 */
	function dump($v) {return is_object($v) ? $this->dumpObject($v) : (
		is_array($v) ? $this->dumpArray($v) : (is_bool($v) ? ju_bts($v) : (is_string($v) ? $v : print_r($v, true)))
	);}

	/**
	 * 2015-01-25
	 * @see ju_kv()
	 * @used-by dumpArray()
	 * @used-by dumpObject()
	 * @param mixed[]|array(string => mixed) $a
	 * @return string
	 */
	function dumpArrayElements(array $a) {return ju_cc_n(ju_map_k(ju_ksort($a), function($k, $v) {return
		"$k: {$this->dump($v)}"
	;}));}

	/**
	 * @used-by dump()
	 * @param mixed $a
	 * @return string
	 */
	private function dumpArray(array $a) {return "[\n" . ju_tab_multiline($this->dumpArrayElements($a)) . "\n]";}

	/**
	 * @used-by dump()
	 * @param object $o
	 * @return string
	 */
	private function dumpObject($o) {/** @var string $r */
		$hash = spl_object_hash($o); /** @var string $hash */
		if (isset($this->_dumped[$hash])) {
			$r = sprintf('[recursion: %s]', get_class($o));
		}
		else {
			$this->_dumped[$hash] = true;
			$r = !ju_has_gd($o)
				? sprintf("%s %s", get_class($o), ju_json_encode_partial($o))
				: sprintf("%s(%s\n)", get_class($o), ju_tab_multiline($this->dumpArrayElements($o->getData())))
			;
		}
		return $r;
	}

	/**
	 * @used-by dumpObject()
	 * @var array(string => bool)
	 */
	private $_dumped = [];

	/**
	 * @return self
	 */
	static function i() {return new self;}
}