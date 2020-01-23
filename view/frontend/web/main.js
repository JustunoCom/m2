// 2019-11-15
define([], function() {return (
	/**
	 * @param {Object} cfg
	 * @param {String} cfg.merchantId
	 * df_is_catalog_product_view():
	 * @param {?String} cfg.action
	 * @param {?Number} cfg.productId
	 * df_is_checkout_success():
	 * @param {?Number} cfg.orderId
	 * @param {?Object} cfg.order
	 */
	function(cfg) {
		window.ju_num = cfg.merchantId;
		window.console.log(`ju_num loaded (${cfg.merchantId})`);
		(function() {
			var k = 'juapp';
			window[k] = window[k] || function() {(window[k].q = window[k].q || []).push(arguments)};
		})();
		juapp('initFunc', function() {
			if (cfg.order) {
				juapp('order', cfg.orderId, cfg.order);
			}
			else require(['df-lodash', 'Magento_Customer/js/customer-data'], function(_, cd) {
				if (cfg.productId) {
					juapp('local', 'pageType', cfg.action);
					juapp('local', 'prodId', cfg.productId);
					(function() {
						var customer = cd.get('customer');
						var updateCustomer = function() {
							juapp('local', 'custId', customer().id);
						};
						updateCustomer();
						customer.subscribe(updateCustomer, this);
					})();
				}
				(function() {
					var cart = cd.get('cart');
					var updateCart = function() {
						var oVal = function(oo, l) {return (
							_.find(oo, function(o) {return l === o['label'].toLowerCase()}) || {}
						).value || null;};
						/**
						 * 2019-11-16
						 * «This function will essentially replace the current Justuno tracked cart items
						 * with the array you provide».
						 * https://support.justuno.com/tracking-visitor-carts-conversions-past-orders
						 */
						juapp('cartItems', _.map(cart().items, function(i) {return {
							color: oVal(i.options, 'color')
							,name: i['product_name']
							,price: i['product_price_value']
							,productid: i['product_id']
							,quantity: i['qty']
							,size: oVal(i.options, 'size')
							,sku: i['product_sku']
							,variationid: i['item_id']
						};}));
					};
					updateCart();
					cart.subscribe(updateCart, this);
				})();
			});
		});
		// 2020-01-24 "Replace `cdn.justuno.com` with `cdn.jst.ai`": https://github.com/justuno-com/m2/issues/8
		require(['//cdn.jst.ai/vck.js'], function() {});
	});
});