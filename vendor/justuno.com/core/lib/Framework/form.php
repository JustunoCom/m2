<?php
use Df\Framework\Form\Element as E;
use Magento\Framework\Data\Form\Element\AbstractElement as AE;
/**
 * 2015-11-28
 * 2020-08-22 "Port the `df_fe_init` function" https://github.com/justuno-com/core/issues/242
 * @used-by \Justuno\M2\Block\GenerateToken::onFormInitialized()
 * @param AE|E $e
 * @param string|object|null $class [optional]
 * $class could be:
 * 1) A class name: «A\B\C».
 * 2) An object. It is reduced to case 1 via @see get_class()
 * @param string|string[] $css [optional]
 * @param array(string => string) $params [optional]
 * @param string|null $path [optional]
 */
function ju_fe_init(AE $e, $class = null, $css = [], $params = [], $path = null) {
	$class = ju_cts($class ?: $e);
	$moduleName = ju_module_name($class); /** @var string $moduleName */
	if (is_null($path)) {
		$classA = ju_explode_class_lc($class); /** @var string[] $classA */
		$classLast = array_pop($classA);
		switch ($classLast) {
			case 'formElement':
			case 'fE':
				break;
			case 'element':
				$path = array_pop($classA);
				break;
			default:
				$path = $classLast;
		}
	}
	$path = ju_ccc('/', 'formElement', $path, 'main');
	$css = ju_array($css);
	if (ju_asset_exists($path, $moduleName, 'less')) {
		$css[]= ju_asset_name($path, $moduleName, 'css');
	}
	$e['before_element_html'] .= ju_cc_n(
		!ju_asset_exists($path, $moduleName, 'js') ? null : ju_js($moduleName, $path, ['id' => $e->getHtmlId()] + $params)
		,ju_link_inline($css)
	);
}