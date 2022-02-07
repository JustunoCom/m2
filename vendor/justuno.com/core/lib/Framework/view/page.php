<?php
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\View\Result\PageFactory;

/**
 * 2017-05-05
 * 2017-05-07
 * $template is a custom root template instead of «Magento_Theme::root.phtml».
 * https://github.com/magento/magento2/blob/2.1.6/app/etc/di.xml#L559-L565
 * «How is the root HTML template (Magento_Theme::root.phtml) declared and implemented?»
 * https://mage2.pro/t/3900
 * 2022-02-22 "Implement a database diagnostic tool": https://github.com/justuno-com/core/issues/347
 * 2021-08-05 @deprecated It is unused.
 * @param string|null $template [optional]
 * @param string ...$handles [optional]
 * @return ResultPage
 */
function ju_page_result($template = null, ...$handles) {
	$f = ju_o(PageFactory::class);/** @var PageFactory $f */
	$r = $f->create(false, ju_clean(['template' => $template])); /** @var ResultPage $r */
	foreach ($handles as $h) {
		$r->addHandle($h);
	}
	return $r;
}