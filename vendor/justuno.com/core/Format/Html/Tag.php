<?php
namespace Justuno\Core\Format\Html;
# 2020-08-22 "Port the `Df\Core\Format\Html\Tag` class" https://github.com/justuno-com/core/issues/254
final class Tag extends \Justuno\Core\O {
	/** @return string */
	private function _render() {return
		"<{$this->openTagWithAttributesAsText()}"
		. ($this->shouldAttributesBeMultiline() ? "\n" : '')
		. (!$this->content() && $this->shortTagAllowed() ? '/>' : ">{$this->content()}</{$this->tag()}>")
	;}
	
	/** @return array(string => string) */
	private function attributes() {return $this->a(self::$P__ATTRIBUTES, []);}
	
	/**
	 * @used-by _render()
	 * @return string
	 */
	private function content() {return juc($this, function() {
		$c = ju_trim(ju_cc_n($this[self::$P__CONTENT]), "\n"); /** @var string $c */
		return $this->tagIs('pre', 'code') || !ju_contains($c, "\n") ? $c :
			"\n" . ju_tab_multiline($c) . "\n"
		;
	});}
	
	/** @return string */
	private function openTagWithAttributesAsText() {return ju_cc_s(
		$this->tag()
		,$this->shouldAttributesBeMultiline() ? "\n" : null
		,call_user_func(
			/** @uses ju_nop() */
			$this->shouldAttributesBeMultiline() ? 'ju_tab_multiline' : 'ju_nop'
			,implode(
				$this->shouldAttributesBeMultiline() ? "\n" :  ' '
				,ju_clean(ju_map_k(function($name, $value) {
					ju_param_sne($name, 0);
					/**
					 * 2015-04-16 Передавать в качестве $value массив имеет смысл, например, для атрибута «class».
					 * 2016-11-29
					 * Не использую @see df_e(), чтобы сохранить двойные кавычки (data-mage-init)
					 * и в то же время сконвертировать одинарные
					 * (потому что значения атрибутов мы ниже обрамляем именно одинарными).
					 * 2017-09-11
					 * Today I have notices that `&apos;` does not work for me
					 * on the Magento 2 backend configuration pages:
					 * @see \Df\Payment\Comment\Description::a()
					 * So I switched to the `&#39;` solution.
					 * «How do I escape a single quote?» https://stackoverflow.com/a/2428595
					 */
					$value = htmlspecialchars(
						str_replace("'", '&#39;', !is_array($value) ? $value : ju_cc_s($value))
						,ENT_NOQUOTES
						,'UTF-8'
						,false
					);
					return '' === $value ? '' : "{$name}='{$value}'";
				}, $this->attributes()))
			)
		)
	);}

	/**
	 * 2018-03-11
	 * Self-closing `span` tags sometimes work incorrectly,
	 * I have encountered it today while working on the frugue.com website.
	 * https://stackoverflow.com/questions/2816833
	 * @return bool
	 */
	private function shortTagAllowed() {return !$this->tagIs('div', 'script', 'span');}

	/** @return bool */
	private function shouldAttributesBeMultiline() {return juc($this, function() {/** @var bool|null $r */return
		!is_null($r = $this[self::$P__MULTILINE]) ? $r : 1 < count($this->attributes())
	;});}

	/**
	 * 2016-08-05
	 * @return string
	 */
	private function tag() {return juc($this, function() {return strtolower($this[self::$P__TAG]);});}

	/**
	 * 2016-08-05
	 * @param string ...$tags
	 * @return bool
	 */
	private function tagIs(...$tags) {return in_array($this->tag(), $tags);}

	/** @var string */
	private static $P__ATTRIBUTES = 'attributes';
	/** @var string */
	private static $P__CONTENT = 'content';
	/** @var string */
	private static $P__MULTILINE = 'multiline';
	/** @var string */
	private static $P__TAG = 'tag';

	/**
	 * @used-by ju_tag()
	 * @param string $tag
	 * @param array(string => string) $attrs [optional]
	 * @param string|null|string[] $content [optional]
	 * @param bool|null $multiline [optional]
	 * @return string
	 */
	static function render($tag, array $attrs = [], $content = null, $multiline = null) {return (new self([
		self::$P__ATTRIBUTES => $attrs
		,self::$P__CONTENT => $content
		,self::$P__MULTILINE => $multiline
		,self::$P__TAG => $tag
	]))->_render();}
}