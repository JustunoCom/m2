<?php
use Exception as E;
use Justuno\Core\Exception as DFE;
use Justuno\Core\Qa\Method as Q;
use Justuno\Core\Zf\Validate\StringT\IntT;

/**
 * 2019-12-14
 * If you do not want the exception to be logged via @see df_bt(),
 * then you can pass an empty string (instead of `null`) as the second argument:
 * @see \Justuno\Core\Exception::__construct():
 *		if (is_null($m)) {
 *			$m = __($prev ? df_ets($prev) : 'No message');
 *			# 2017-02-20 To facilite the «No message» diagnostics.
 *			if (!$prev) {
 *				df_bt();
 *			}
 *		}
 * https://github.com/mage2pro/core/blob/5.5.7/Core/Exception.php#L61-L67
 * 2020-06-17 "Port the `df_assert` function": https://github.com/justuno-com/core/issues/33
 * @used-by ju_assert_qty_supported()
 * @used-by ju_catalog_locator()
 * @used-by ju_eta()
 * @used-by ju_layout_update()
 * @used-by ju_module_dir()
 * @used-by ju_oqi_amount()
 * @used-by juaf()
 * @used-by \Justuno\Core\Qa\Trace\Frame::methodParameter()
 * @used-by \Justuno\M2\Store::v()
 * @param mixed $cond
 * @param string|E|null $m [optional]
 * @return mixed
 * @throws DFE
 */
function ju_assert($cond, $m = null) {return $cond ?: ju_error($m);}

/**
 * 2017-01-14
 * 2020-08-19 "Port the `df_assert_nef` function" https://github.com/justuno-com/core/issues/201
 * @used-by \Justuno\Core\Qa\Trace\Frame::context()
 * @param mixed $v
 * @param string|E $m [optional]
 * @return mixed
 * @throws DFE
 */
function ju_assert_nef($v, $m = null) {return false !== $v ? $v : ju_error($m ?:
	'The «false» value is rejected, any others are allowed.'
);}

/**
 * 2020-06-22 "Port the `df_assert_sne` function": https://github.com/justuno-com/core/issues/115
 * @used-by ju_currency_base()
 * @used-by ju_file_name()
 * @param string $v
 * @param int $sl [optional]
 * @return string
 * @throws DFE
 */
function ju_assert_sne($v, $sl = 0) {
	$sl++;
	Q::assertValueIsString($v, $sl);
	# The previous code `if (!$v)` was wrong because it rejected the '0' string.
	return '' !== strval($v) ? $v : Q::raiseErrorVariable(__FUNCTION__, $ms = [Q::NES], $sl);
}

/**
 * 2016-08-09
 * 2020-08-21 "Port the `ju_assert_traversable` function" https://github.com/justuno-com/core/issues/222
 * @used-by juaf()
 * @param \Traversable|array $v
 * @param string|E|null $m [optional]
 * @return \Traversable|array
 * @throws DFE
 */
function ju_assert_traversable($v, $m = null) {return ju_check_traversable($v) ? $v : ju_error($m ?:
	'A variable is expected to be a traversable or an array, ' . 'but actually it is %s.', ju_type($v)
);}

/**
 * 2021-03-06 "Port the `df_bool` function": https://github.com/justuno-com/core/issues/356
 * @used-by \Justuno\Core\Config\Settings::b()
 * @param mixed $v
 * @return bool
 */
function ju_bool($v) {
	/**
	 * Unfortunately, we can not replace @uses in_array() with @see array_flip() + @see isset() to speedup the execution,
	 * because it could lead to the warning: «Warning: array_flip(): Can only flip STRING and INTEGER values!».
	 * Moreover, @see array_flip() + @see isset() fails the following test:
	 *	$a = array(null => 3, 0 => 4, false => 5);
	 *	$this->assertNotEquals($a[0], $a[false]);
	 * Though, @see array_flip() + @see isset() does not fail the tests:
	 * $this->assertNotEquals($a[null], $a[0]);
	 * $this->assertNotEquals($a[null], $a[false]);
	 */
	static $no = [0, '0', 'false', false, null, 'no', 'off', '']; /** @var mixed[] $no */
	static $yes = [1, '1', 'true', true, 'yes', 'on']; /** @var mixed[] $yes */
	/**
	 * Passing $strict = true to the @uses in_array() call is required here,
	 * otherwise any true-compatible value (e.g., a non-empty string) will pass the check.
	 */
	return in_array($v, $no, true) ? false : (in_array($v, $yes, true) ? true :
		ju_error('A boolean value is expected, but got «%s».', ju_dump($v))
	);
}

/**
 * 2020-08-23 "Port the `ju_int` function" https://github.com/justuno-com/core/issues/287
 * @used-by ju_nat()
 * @used-by ju_product_id()
 * @used-by \Justuno\Core\Zf\Validate\IntT::filter()
 * @param mixed|mixed[] $v
 * @param bool $allowNull [optional]
 * @return int|int[]
 * @throws DFE
 */
function ju_int($v, $allowNull = true) {/** @var int|int[] $r */
	if (is_array($v)) {
		$r = ju_map(__FUNCTION__, $v, $allowNull);
	}
	elseif (is_int($v)) {
		$r = $v;
	}
	elseif (is_bool($v)) {
		$r = $v ? 1 : 0;
	}
	elseif ($allowNull && (is_null($v) || ('' === $v))) {
		$r = 0;
	}
	elseif (!IntT::s()->isValid($v)) {
		ju_error(IntT::s()->getMessage());
	}
	else {
		$r = (int)$v;
	}
	return $r;
}

/**
 * 2015-04-13
 * 1) It does not validate item types (unlike @see df_int() )
 * 2) It works only with arrays.
 * 3) Keys are preserved: http://3v4l.org/NHgdK
 * @used-by ju_fetch_col_int()
 * @param mixed[] $values
 * @return int[]
 */
function ju_int_simple(array $values) {return array_map('intval', $values);}

/**
 * 2020-08-23 "Port the `df_nat` function" https://github.com/justuno-com/core/issues/289
 * @used-by \Justuno\M2\Controller\Cart\Add::execute()
 * @used-by \Justuno\M2\Controller\Cart\Add::product()
 * @param mixed $v
 * @param bool $allow0 [optional]
 * @return int
 * @throws DFE
 */
function ju_nat($v, $allow0 = false) {/** @var int $r */
	$r = ju_int($v, $allow0);
	$allow0 ? ju_assert_ge(0, $r) : ju_assert_gt0($r);
	return $r;
}