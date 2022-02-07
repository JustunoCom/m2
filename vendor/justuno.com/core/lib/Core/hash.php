<?php
use Magento\Framework\Model\AbstractModel as M;
/**
 * 2020-06-13 "Port the `df_hash_a` function": https://github.com/justuno-com/core/issues/6
 * @used-by ju_hash_a()
 * @used-by juc()
 * @used-by jucf()
 * @param mixed[] $a
 * @return string
 */
function ju_hash_a(array $a) {
	$resultA = []; /** @var string[] $resultA */
	foreach ($a as $k => $v) {
		/** @var int|string $k */ /** @var mixed $v */
		$resultA[]= "$k=>" . (is_object($v) ? ju_hash_o($v) : (is_array($v) ? ju_hash_a($v) : $v));
	}
	return implode('::', $resultA);
}

/**
 * 2020-06-13 "Port the `df_hash_o` function": https://github.com/justuno-com/core/issues/7
 * @used-by ju_hash_a()
 * @param object $o
 * @return string
 */
function ju_hash_o($o) {
	/**
	 * 2016-09-05
	 * Для ускорения заменил вызов df_id($o, true) на инлайновыый код.
	 * @see df_id()
	 */
	$r = $o instanceof M || method_exists($o, 'getId') ? $o->getId() : null; /** @var string $r */
	return $r ? get_class($o) . "::$r" : spl_object_hash($o);
}