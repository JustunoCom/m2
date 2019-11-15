// 2019-11-15
define([], function() {return (
	/**
	 * @param {Object} c
	 * @param {String} c.merchantId
	 */
	function(c) {
		window.ju_num = c.merchantId;
		window.console.log(`ju_num loaded (${c.merchantId})`);
		var r = 'juapp';
		window[r] = window[r] || function() {(window[r].q = window[r].q || []).push(arguments)};
		require(['df-lodash', 'jquery', '//cdn.justuno.com/vck.js'], function(_, $) {
			debugger;
			if ($('body').hasClass('catalog-product-view')) {
				debugger;
			}
		});
	});
});