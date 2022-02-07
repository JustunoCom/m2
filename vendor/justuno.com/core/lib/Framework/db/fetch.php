<?php
use Magento\Framework\DB\Select as S;

/**
 * 2019-11-15
 * 2020-08-22 "Port the `ju_fetch` function" https://github.com/justuno-com/core/issues/266
 * @used-by \Justuno\M2\Config\Source\Brand::map()
 * @param string $t
 * @param string|string[] $cols [optional]
 * @param string|null $compareK [optional]
 * @param int|string|int[]|string[]|null $compareV [optional]
 * @return array(array(string => string))
 */
function ju_fetch($t, $cols = '*', $compareK = null, $compareV = null) {
	$s = ju_db_from($t, $cols); /** @var S $s */
	if (!is_null($compareV)) {
		$s->where($compareK . ' ' . ju_sql_predicate_simple($compareV), $compareV);
	}
	return ju_conn()->fetchAll($s);
}

/**
 * 2015-04-13
 * @used-by ju_fetch_col_int()
 * @param string $t
 * @param string $col
 * @param string|null $compareK [optional]
 * @param int|string|int[]|string[]|null $compareV [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function ju_fetch_col($t, $col, $compareK = null, $compareV = null, $distinct = false) {
	$s = ju_db_from($t, $col); /** @var S $s */
	if (!is_null($compareV)) {
		$s->where(($compareK ?: $col) . ' ' . ju_sql_predicate_simple($compareV), $compareV);
	}
	$s->distinct($distinct);
	return ju_conn()->fetchCol($s, $col);
}

/**
 * 2015-04-13
 * @used-by \Justuno\M2\Catalog\Diagnostic::p()
 * @param string $t
 * @param string $cSelect
 * @param string|null $compareK [optional]
 * @param int|string|int[]|string[]|null $compareV [optional]
 * @param bool $distinct [optional]
 * @return int[]|string[]
 */
function ju_fetch_col_int($t, $cSelect, $compareK = null, $compareV = null, $distinct = false) {return
	/** I intentionally do not use @see df_int() to gain speed */
	ju_int_simple(ju_fetch_col($t, $cSelect, $compareK, $compareV, $distinct))
;}

/**
 * 2015-11-03
 * 2020-08-24 "Port the `df_fetch_one` function" https://github.com/justuno-com/core/issues/327
 * @used-by \Justuno\M2\Catalog\Diagnostic::p()
 * @param string $t
 * @param string|string[] $cols
 * @param array(string => string) $compare
 * @return string|null|array(string => mixed)
 */
function ju_fetch_one($t, $cols, $compare) {
	$s = ju_db_from($t, $cols); /** @var S $s */
	foreach ($compare as $c => $v) {/** @var string $c */ /** @var string $v */
		$s->where('? = ' . $c, $v);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return '*' !== $cols ? ju_ftn(ju_conn()->fetchOne($s)) : ju_eta(ju_conn()->fetchRow($s, [], \Zend_Db::FETCH_ASSOC));
}