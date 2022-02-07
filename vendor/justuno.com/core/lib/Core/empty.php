<?php
/**
 * 2017-04-26
 * 2020-08-26 "Port the `df_eta` function" https://github.com/justuno-com/core/issues/329
 * @used-by ju_fetch_one()
 * @param mixed|null $v
 * @return mixed[]
 */
function ju_eta($v) {
	if (!is_array($v)) {
		ju_assert(empty($v));
		$v = [];
	}
	return $v;
}

/**
 * 2020-01-29
 * 2020-08-14 "Port the `df_etn` function" https://github.com/justuno-com/core/issues/181
 * @used-by ju_customer_session_id()
 * @param mixed $v
 * @return mixed|null
 */
function ju_etn($v) {return $v ?: null;}

/**
 * 2020-08-26 "Port the `df_ftn` function" https://github.com/justuno-com/core/issues/328
 * @used-by ju_fetch_one()
 * @param mixed|false $v
 * @return mixed|null
 */
function ju_ftn($v) {return (false === $v) ? null : $v;}

/**
 * 2020-06-14 "Port the `df_nes` function": https://github.com/justuno-com/core/issues/19
 * @used-by jua_deep()
 * @param mixed $v
 * @return bool
 */
function ju_nes($v) {return is_null($v) || '' === $v;}

/**
 * 2020-06-20 "Port the `df_nts` function": https://github.com/justuno-com/core/issues/89
 * @used-by ju_trim()
 * @used-by \Justuno\Core\Qa\Trace\Frame::className()
 * @used-by \Justuno\Core\Qa\Trace\Frame::functionName()
 * @param mixed|null $v
 * @return mixed
 */
function ju_nts($v) {return !is_null($v) ? $v : '';}