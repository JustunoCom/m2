<?php
namespace Justuno\Core;
/**
 * 2017-07-13
 * 2020-08-19 "Port the `Df\Core\O` class" https://github.com/justuno-com/core/issues/195
 * @see \Justuno\Core\Format\Html\Tag
 * @see \Justuno\Core\Qa\Message
 * @see \Justuno\Core\Qa\Trace\Frame
 */
class O implements \ArrayAccess {
	/**
	 * 2017-07-13
	 * @used-by \Justuno\Core\Qa\Message\Failure\Error::i()
	 * @used-by \Justuno\Core\Qa\Message\Failure\Exception::i()
	 * @param array(string => mixed) $a [optional]
	 */
	final function __construct(array $a = []) {$this->_a = $a;}

	/**
	 * 2017-07-13
	 * @used-by \Justuno\Core\Qa\Message\Failure::postface()
	 * @param string|string[]|null $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function a($k = null, $d = null) {return jua($this->_a, $k, $d);}

	/**
	 * 2017-07-13
	 * @return string
	 */
	function j() {return ju_json_encode($this->_a);}

	/**
	 * 2017-07-13
	 * «This method is executed when using isset() or empty() on objects implementing ArrayAccess.
	 * When using empty() ArrayAccess::offsetGet() will be called and checked if empty
	 * only if ArrayAccess::offsetExists() returns TRUE».
	 * http://php.net/manual/arrayaccess.offsetexists.php
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @param string $k
	 * @return bool
	 */
	function offsetExists($k) {return !is_null(jua_deep($this->_a, $k));}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $k
	 * @return array(string => mixed)|mixed|null
	 */
	function offsetGet($k) {return jua_deep($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $k
	 * @param mixed $v
	 */
	function offsetSet($k, $v) {jua_deep_set($this->_a, $k, $v);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $k
	 */
	function offsetUnset($k) {jua_deep_unset($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @used-by __construct()
	 * @used-by a()
	 * @var array(string => mixed)
	 */
	private $_a;
}