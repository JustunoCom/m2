<?php
namespace Justuno\Core\Framework\Form;
/**
 * 2015-11-24
 * 2020-08-21 "Port the `Df\Framework\Form\ElementI` interface" https://github.com/justuno-com/core/issues/236
 * @see \Justuno\M2\Block\GenerateToken
 */
interface ElementI {
	/**
	 * 2015-11-24 Many operations on the element require the form's existance, so we do them here.
	 * @used-by \Df\Framework\Plugin\Data\Form\Element\AbstractElement::afterSetForm()
	 * @see \Justuno\M2\Block\GenerateToken::onFormInitialized()
	 */
	function onFormInitialized();

	# 2015-11-24
	const AFTER = 'after';
	const BEFORE = 'before';
}


