// 2019-11-15
define(['df-lodash', 'jquery'], function(_, $) {return (
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
			window[k] = window[k] || function() {
				(window[k].q = window[k].q || []).push(arguments)
			};
		})();
		juapp('initFunc', function() {
			if (cfg.order) {
				juapp('order', cfg.orderId, cfg.order);
			}
			else {
				if (cfg.productId) {
					juapp('local', 'pageType', cfg.action);
					juapp('local', 'prodId', cfg.productId);
					//juapp('local', 'custId', ju_MageProductView.CustomerID);
					//console.log('product view logged' + ' - ' + ju_MageProductView.CustomerID);
				}
				var options = {};
				var updateJustunoCart = function() {
					$
						.ajax({dataType: 'json', type: 'GET', url: '/customer/section/load/?sections=cart'})
						.done(function(res) {
							var items = res.cart.items;
							Object.values(items).forEach(function(i) {
								i.options = i.options.map(function(o) {
									var r = {};
									r[o.label] = _.omit(o, 'label');
									return JSON.stringify(r);
								});
							});
							for (var i = 0; i < Object.values(items).length; i++) {
								options[items[i].item_id] = items[i].options;
							}
							var r = [];
							items.forEach(function(i) {
								var o = options[i.item_id];
								r.push({
									color: _.get(o, 'Color.value', _.get(o, 'color.value', null))
									,name: i['product_name']
									,price: i['product_price_value']
									,productid: i['product_id']
									,quantity: i['qty']
									,size: _.get(o, 'Size.value', _.get(o, 'size.value', null))
									,sku: i['product_sku']
									,variationid: i['item_id']
								});
							});
							/**
							 * 2019-11-16
							 * «This function will essentially replace the current Justuno tracked cart items
							 * with the array you provide».
							 * https://support.justuno.com/tracking-visitor-carts-conversions-past-orders
							 */
							juapp('cartItems', r);
						})
						.fail(function (xhr, status, err) {
							var errorMessage = xhr.status + ': ' + status + '...' + err;
							alert('Error - ' + errorMessage);
						});
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
			}
		});
		require(['//cdn.justuno.com/vck.js'], function() {});
	});
});