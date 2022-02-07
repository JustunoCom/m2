<?php
use Justuno\Core\Helper\Text as T;
use Magento\Framework\Phrase as P;
/**
 * 2015-11-22
 * 2021-02-25 @deprecated It is unused.
 * @param string|string[]|P|P[] $s
 * @return string|string[]
 */
function ju_quote_double($s) {return ju_t()->quote($s, T::QUOTE__DOUBLE);}

/**
 * @used-by \Justuno\M2\Catalog\Diagnostic::p()
 * @param string|string[]|P|P[] $s
 * @return string|string[]
 */
function ju_quote_russian($s) {return ju_t()->quote($s, T::QUOTE__RUSSIAN);}

/**
 * 2021-02-25 @deprecated It is unused.
 * @param string|string[]|P|P[] $s
 * @return string|string[]
 */
function ju_quote_single($s) {return ju_t()->quote($s, T::QUOTE__SINGLE);}