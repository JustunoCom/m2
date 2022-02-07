<?php
namespace Justuno\Core\Zf\Filter;
# 2020-06-20 "Port the `Justuno\Core\Zf\Filter\StringTrim` class": https://github.com/justuno-com/core/issues/90
/** @used-by ju_trim() */
class StringTrim extends \Zend_Filter_StringTrim {
	/**
	 * @override
	 * @param string $value
	 * @param string $charlist
	 * @return string
	 */
	protected function _unicodeTrim($value, $charlist = '\\\\s') {
		if ('' === $value) {
			$result = $value;
		}
		else {
			$chars = preg_replace(
				['/[\^\-\]\\\]/S', '/\\\{4}/S', '/\//'],
				['\\\\\\0', '\\', '\/'],
				$charlist
			);
  			$pattern = '/^[' . $chars . ']+|[' . $chars . ']+$/usSD';
			$result = preg_replace($pattern, '', $value);
			if (null === $result) {
				$result = $this->_slowUnicodeTrim($value, $charlist);
			}
		}
		return $result;
	}

	/**
	 * @param $value
	 * @param $chars
	 * @return string
	 */
	private function _slowUnicodeTrim($value, $chars) {
		$utfChars = $this->_splitUtf8($value);
		$pattern = '/^[' . $chars . ']$/usSD';
		while ($utfChars && preg_match($pattern, $utfChars[0])) {
			array_shift($utfChars);
		}
		while ($utfChars && preg_match($pattern, $utfChars[count($utfChars) - 1])) {
			array_pop($utfChars);
		}
		return implode($utfChars);
	}

	/**
	 * @param $value
	 * @return array|bool
	 */
	private function _splitUtf8($value) {
		try {
			$utfChars = str_split(iconv('UTF-8', 'UTF-32BE', $value), 4);
		}
		catch (\Exception $e) {
			ju_error('The value is not encoded in UTF-8: «%s».', $value);
		}
		array_walk($utfChars, create_function('&$char', '$char = iconv("UTF-32BE", "UTF-8", $char);'));
		return $utfChars;
	}
}