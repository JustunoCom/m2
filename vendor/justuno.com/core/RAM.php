<?php
namespace Justuno\Core;
use Justuno\Core\ICached;
# 2020-06-13 "Port the `Df\Core\RAM` class": https://github.com/justuno-com/core/issues/9
final class RAM {
	/**
	 * 2020-06-13
	 * @param string $tag
	 */
	function clean($tag) {
		if (isset($this->_tags[$tag])) {
			foreach ($this->_tags[$tag] as $k) { /** @var string $k */
				unset($this->_data[$k]);
			}
			unset($this->_tags[$tag]);
		}
	}

	/**
	 * 2020-06-13
	 * The following code will return `1`:
	 * 		$a = ['a' => null];
	 * 		echo intval(array_key_exists('a', $a));
	 * https://3v4l.org/9cQOO
	 * @used-by jucf()
	 * @used-by get()
	 * @param string $k
	 * @return bool
	 */
	function exists($k) {return array_key_exists($k, $this->_data);}

	/**
	 * 2020-06-13
	 * @used-by jucf()
	 * @param string $k
	 * @return mixed
	 */
	function get($k) {return $this->exists($k) ? $this->_data[$k] : null;}

	/**
	 * 2020-06-13
	 */
	function reset() {$this->_data = []; $this->_tags = [];}

	/**
	 * 2020-06-13
	 * @used-by jucf()
	 * @param string $k
	 * @param mixed $v
	 * @param string[] $tags [optional]
	 * @return mixed
	 */
	function set($k, $v, $tags = []) {
		if ($v instanceof ICached) {
			$tags += $v->tags();
		}
		$this->_data[$k] = $v;
		foreach ($tags as $tag) { /** @var string $tag */
			if (!isset($this->_tags[$tag])) {
				$this->_tags[$tag] = [$k];
			}
			else if (!in_array($k, $this->_tags[$tag])) {
				$this->_tags[$tag][] = $k;
			}
		}
		return $v;
	}

	/**
	 * 2017-08-10
	 * @used-by clean()
	 * @used-by exists()
	 * @used-by get()
	 * @used-by set()
	 * @var array(string => mixed)	«Cache Key => Cached Data»
	 */
	private $_data = [];

	/**
	 * 2017-08-10
	 * @used-by clean()
	 * @used-by set()
	 * @var array(string => string[])  «Tag ID => Cache Keys»
	 */
	private $_tags = [];

	/** 2017-08-10 @return self */
	static function s() {static $r; return $r ? $r : $r = new self;}
}