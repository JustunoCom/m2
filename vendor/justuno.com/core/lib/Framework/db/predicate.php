<?php
/**
 * 2015-04-13
 * 2020-08-23 "Port the `df_sql_predicate_simple` function" https://github.com/justuno-com/core/issues/268
 * @used-by ju_fetch()
 * @used-by ju_fetch_col()
 * @param int|string|int[]|string[] $v
 * @param bool $not [optional]
 * @return string
 */
function ju_sql_predicate_simple($v, $not = false) {return
	is_array($v) ? ($not ? 'NOT IN (?)' : 'IN (?)') : ($not ? '<> ?' : '= ?')
;}