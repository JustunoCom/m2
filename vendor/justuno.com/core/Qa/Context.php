<?php
namespace Justuno\Core\Qa;
# 2020-06-17 "Port the `Df\Qa\Context` class": https://github.com/justuno-com/core/issues/55
class Context {
	/**
	 * @param string $label
	 * @param string $value
	 * @param int $weight [optional]
	 * @param array(array(string => string|int)) $params $params
	 */
	static function add($label, $value, $weight = 0) {
		self::$_items[$label] = [self::$VALUE => $value, self::$WEIGHT => $weight];
	}

	/**
	 * @used-by \Justuno\Core\Qa\Message::report()
	 * @return string
	 */
	static function render() {/** @var string $r */
		# 2015-09-02 Warning: max(): Array must contain at least one element.
		if (!self::$_items) {
			$r = '';
		}
		else {
			uasort(self::$_items, [__CLASS__, 'sort']); /** @uses Context::sort() */
			$padSize = 2 + max(array_map('mb_strlen', array_keys(self::$_items))); /** @var int $padSize */
			$r = ju_kv(ju_each(self::$_items, self::$VALUE), $padSize);
		}
		return $r;
	}

	/**
	 * @used-by render()
	 * @used-by uasort()
	 * @param array(string => string|int) $a
	 * @param array(string => string|int) $b
	 * @return int
	 */
	private static function sort(array $a, array $b) {return $a[self::$WEIGHT] - $b[self::$WEIGHT];}

	/** @var array(string => string) */
	private static $_items = [];

	/** @var string */
	private static $VALUE = 'value';
	/** @var string */
	private static $WEIGHT = 'weight';
}