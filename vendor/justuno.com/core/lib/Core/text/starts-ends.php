<?php
/**
 * 2020-06-17 "Port the `df_ends_with` function": https://github.com/justuno-com/core/issues/47
 * @used-by ju_append()
 * @used-by ju_is_bin_magento()
 * @used-by \Justuno\Core\Qa\Trace\Frame::isClosure()
 * @used-by \Justuno\Core\Sentry\Trace::get_frame_context()
 * @used-by \Justuno\M2\Plugin\Framework\App\Router\ActionList::aroundGet()
 * @param string $haystack
 * @param string|string[] $needle
 * @return bool
 */
function ju_ends_with($haystack, $needle) {return is_array($needle)
	? null !== ju_find($needle, __FUNCTION__, [], [$haystack])
	: 0 === ($l = mb_strlen($needle)) || $needle === mb_substr($haystack, -$l)
;}

/**
 * 2020-06-16 "Port the `df_starts_with` function": https://github.com/justuno-com/core/issues/30
 * @used-by ju_action_prefix()
 * @used-by ju_check_url_absolute()
 * @used-by ju_package()
 * @used-by ju_path_is_internal()
 * @used-by ju_prepend()
 * @used-by \Justuno\Core\Framework\Plugin\Data\Form\Element\AbstractElement::afterGetElementHtml()
 * @used-by \Justuno\Core\Qa\Trace::__construct()
 * @used-by \Justuno\Core\Zf\Validate\StringT\IntT::isValid()
 * @param string $haystack
 * @param string|string[] $needle
 * @return bool
 */
function ju_starts_with($haystack, $needle) {return is_array($needle)
	? null !== ju_find($needle, __FUNCTION__, [], [$haystack])
	: $needle === mb_substr($haystack, 0, mb_strlen($needle))
;}

