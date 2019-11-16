// 2019-11-15
define(['df-lodash', 'jquery', 'Magento_Customer/js/customer-data'], function(_, $, customer) {return (
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
			else require(['Magento_Customer/js/customer-data'], function(cd) {
				debugger;
				if (cfg.productId) {
					juapp('local', 'pageType', cfg.action);
					juapp('local', 'prodId', cfg.productId);
					//juapp('local', 'custId', ju_MageProductView.CustomerID);
					//console.log('product view logged' + ' - ' + ju_MageProductView.CustomerID);
				}
				var cart = cd.get('cart');
				var updateJustunoCart = function() {
					$
						.ajax({dataType: 'json', type: 'GET', url: '/customer/section/load/?sections=cart'})
						.fail(function (xhr, status, err) {alert(`Error - ${xhr.status}: ${status}...${err}`);})
						.done(function(res) {
							var oVal = function(oo, l) {return (
								_.find(oo, function(o) {return l === o['label'].toLowerCase()}) || {}
							).value || null;};
							/**
							 * 2019-11-16
							 * «This function will essentially replace the current Justuno tracked cart items
							 * with the array you provide».
							 * https://support.justuno.com/tracking-visitor-carts-conversions-past-orders
							 */
							juapp('cartItems', _.map(res.cart.items, function(i) {return {
								color: oVal(i.options, 'color')
								,name: i['product_name']
								,price: i['product_price_value']
								,productid: i['product_id']
								,quantity: i['qty']
								,size: oVal(i.options, 'size')
								,sku: i['product_sku']
								,variationid: i['item_id']
							};}));
						})
					;

				};
				updateJustunoCart();
				(function() {
					// 2019-11-16 https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/open#Syntax
					var _super = XMLHttpRequest.prototype.open;
					XMLHttpRequest.prototype.open = function(method, url) {
						if (-1 < url.indexOf('?sections=cart')
							&& (-1 < url.indexOf('%2Cmessages') || -1 < url.indexOf('update_section_id'))
						) {
							this.addEventListener('load', updateJustunoCart);
						}
						_super.apply(this, arguments);
					};
				})();
			});
		});
		require(['//cdn.justuno.com/vck.js'], function() {});
	});
});