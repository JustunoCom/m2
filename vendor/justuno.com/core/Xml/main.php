<?php
/**
 * 2020-08-14 "Port the `df_xml_output_plain` function" https://github.com/justuno-com/core/issues/193
 * @used-by ju_xml_output_plain()
 */
const JU_XML_BEGIN = '{df-xml}';
const JU_XML_END = '{/df-xml}';

/**
 * 2020-08-14 "Port the `df_xml_output_plain` function" https://github.com/justuno-com/core/issues/193
 * @used-by \Justuno\Core\Qa\Message::sections()
 * @param string ...$args
 * @return string|string[]
 */
function ju_xml_output_plain(...$args) {return ju_call_a(function($s) {return str_replace(
	[JU_XML_BEGIN, JU_XML_END], null, $s
);}, $args);}