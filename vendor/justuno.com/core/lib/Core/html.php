<?php
use Justuno\Core\Format\Html\Tag;
/**
 * 2015-10-27
 * 2020-08-22 "Port the `ju_link_inline` function" https://github.com/justuno-com/core/issues/247
 * @used-by df_fe_init()
 * @param string ...$args
 * @return string
 */
function ju_link_inline(...$args) {return ju_call_a(function($res) {return ju_resource_inline(
	$res, function($url) {return ju_tag('link', ['href' => $url, 'rel' => 'stylesheet', 'type' => 'text/css'], null, false);}
);}, $args);}

/**
 * 2015-12-11
 * 2020-08-22 "Port the `df_resource_inline` function" https://github.com/justuno-com/core/issues/256
 * @used-by ju_link_inline()
 * @param string $r
 * @param \Closure $f
 * @return string
 */
function ju_resource_inline($r, \Closure $f) {
	static $c; /** @var array(string => bool) $c */
	if (!$r || isset($c[$r])) {$result = '';}
	else {$c[$r] = true; $result = $f(ju_asset_create($r)->getUrl());}
	return $result;
}

/**
 * 2015-04-16
 * From now on you can pass an array as an attribute's value: @see \Df\Core\Format\Html\Tag::getAttributeAsText()
 * It can be useful for attrivutes like `class`.
 * 2016-05-30 From now on $attrs could be a string. It is the same as ['class' => $attrs].
 * 2020-08-22 "Port the `df_tag` function" https://github.com/justuno-com/core/issues/253
 * @used-by ju_js_x()
 * @used-by ju_kv_table()
 * @used-by ju_link_inline()
 * @param string $tag
 * @param string|array(string => string|string[]|int|null) $attrs [optional]
 * @param string|null|string[] $content [optional]
 * @param bool|null $multiline [optional]
 * @return string
 */
function ju_tag($tag, $attrs = [], $content = null, $multiline = null) {return Tag::render(
	$tag, is_array($attrs) ? $attrs : ['class' => $attrs], $content, $multiline
);}