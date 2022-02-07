<?php
use Justuno\Core\O;
use Justuno\Core\Theme\Model\View\Design as DfDesign;
use Magento\Backend\Block\Template as BackendTemplate;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Layout;
use Magento\Framework\View\LayoutInterface as ILayout;
use Magento\Framework\View\Layout\ProcessorInterface as IProcessor;
use Magento\Framework\View\Model\Layout\Merge;

/**
 * 2020-08-22 "Port the `df_block` function" https://github.com/justuno-com/core/issues/238
 * @used-by \Justuno\M2\Block\GenerateToken::getElementHtml()
 * @param string|O|null $c
 * @param string|array(string => mixed) $data [optional]
 * @param string|null $template [optional]
 * @param array $vars [optional]
 * @return AbstractBlock|BlockInterface|Template
 */
function ju_block($c, $data = [], $template = null, array $vars = []) {
	if (is_null($c)) {
		$c = ju_is_backend() ? BackendTemplate::class : Template::class;
	}
	/** @var string|null $template */
	if (is_string($data)) {
		$template = $data;
		$data = [];
	}
	/** @var AbstractBlock|BlockInterface|Template $r */
	$r = ju_layout()->createBlock($c, jua($data, 'name'), ['data' => $data]);
	# 2019-06-11
	if ($r instanceof Template) {
		# 2016-11-22
		$r->assign($vars);
	}
	if ($template && $r instanceof Template) {
		$r->setTemplate(ju_file_ext_add($template, 'phtml'));
	}
	return $r;
}

/**
 * 2020-08-22 "Port the `df_layout` function" https://github.com/justuno-com/core/issues/239
 * @used-by ju_block()
 * @used-by ju_layout_update()
 * @return Layout|ILayout
 */
function ju_layout() {return ju_o(ILayout::class);}

/**
 * 2017-10-16
 * 2020-08-23 "Port the `df_layout_update` function" https://github.com/justuno-com/core/issues/301
 * @used-by ju_handles()
 * @param \Closure|bool|mixed $onError [optional]
 * @return IProcessor|Merge
 */
function ju_layout_update($onError = true) {return ju_try(function() {
	ju_assert(DfDesign::isThemeInitialized(),
		'This attempt to call Magento\Framework\View\Layout::getUpdate() can break the Magento frontend.'
	);
	return ju_layout()->getUpdate();
}, $onError);}