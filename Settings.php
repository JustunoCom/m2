<?php
namespace Justuno\M2;
/**
 * 2021-03-06
 * @method static Settings s()
 */
final class Settings extends \Justuno\Core\Config\Settings {
	/**
	 * 2021-03-06
	 * @used-by \Justuno\M2\Block\Js::_toHtml()
	 * @return string
	 */
	function accid() {return $this->v();}

	/**
	 * 2021-03-06
	 * @used-by \Justuno\M2\Controller\Response\Catalog::execute()
	 * @return string
	 */
	function brand_attribute() {return $this->v();}

	/**
	 * 2021-03-06
	 * @override
	 * @see \Justuno\Core\Config\Settings::prefix()
	 * @used-by \Justuno\Core\Config\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'justuno_settings/options_interface';}
}