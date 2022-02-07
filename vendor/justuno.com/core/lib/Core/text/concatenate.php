<?php
/**
 * 2020-08-19 "Port the `df_cc` function" https://github.com/justuno-com/core/issues/198
 * @see ju_ccc()
 * @used-by \Justuno\Core\Qa\Trace\Formatter::frame()
 * @param string $glue
 * @param string|string[] ...$elements
 * @return string
 */
function ju_cc($glue, ...$elements) {return implode($glue, jua_flatten($elements));}

/**
 * 2020-06-18 "Port the `df_cc_n` function": https://github.com/justuno-com/core/issues/63
 * @used-by ju_fe_init()
 * @used-by ju_kv()
 * @used-by ju_log_l()
 * @used-by ju_tab_multiline()
 * @used-by \Justuno\Core\Format\Html\Tag::content()
 * @used-by \Justuno\Core\Qa\Dumper::dumpArrayElements()
 * @used-by \Justuno\Core\Qa\Method::raiseErrorParam()
 * @used-by \Justuno\Core\Qa\Method::raiseErrorResult()
 * @used-by \Justuno\Core\Qa\Method::raiseErrorVariable()
 * @used-by \Justuno\Core\Qa\Trace\Formatter::frame()
 * @param string|string[] ...$args
 * @return string
 */
function ju_cc_n(...$args) {return ju_ccc("\n", jua_flatten($args));}

/**
 * 2020-06-21 "Port the `df_cc_path` function": https://github.com/justuno-com/core/issues/103
 * @used-by ju_cfg()
 * @used-by ju_file_name()
 * @used-by ju_js_x()
 * @used-by ju_module_path()
 * @used-by ju_module_path_etc()
 * @used-by ju_url_trim_index()
 * @used-by \Justuno\Core\Config\Backend::value()
 * @param string|string[] ...$args
 * @return string
 */
function ju_cc_path(...$args) {return ju_ccc('/', jua_flatten($args));}

/**
 * 2016-08-10
 * 2020-08-21 "Port the `df_cc_s` function" https://github.com/justuno-com/core/issues/210
 * @used-by ju_cli_cmd()
 * @used-by \Justuno\Core\Format\Html\Tag::openTagWithAttributesAsText()
 * @param string|string[] ...$args
 * @return string
 */
function ju_cc_s(...$args) {return ju_ccc(' ', jua_flatten($args));}

/**
 * 2020-06-18 "Port the `df_ccc` function": https://github.com/justuno-com/core/issues/57
 * @used-by ju_asset_name()
 * @used-by ju_cc_method()
 * @used-by ju_cc_n()
 * @used-by ju_cc_path()
 * @used-by ju_cc_s()
 * @used-by ju_fe_init()
 * @used-by ju_log_l()
 * @used-by ju_url_bp()
 * @used-by \Justuno\Core\Qa\Message::reportName()
 * @used-by \Justuno\M2\Setup\UpgradeSchema::tr()
 * @param string $glue
 * @param string|string[] ...$elements
 * @return string
 */
function ju_ccc($glue, ...$elements) {return implode($glue, ju_clean(jua_flatten($elements)));}