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
				var push = function() {
					$
						.ajax({dataType: 'json', type: 'GET', url: '/customer/section/load/?sections=cart'})
						.then(function(res) {
							Object.values(res.cart.items).forEach(function(i) {
								i.options = i.options.map(function(o) {
									var r = {};
									r[o.label] = JSON.parse(JSON.stringify(_.omit(o, 'label')));
									return JSON.stringify(r);
								});
							});
							for (var i = 0; i < Object.values(res.cart.items).length; i++) {
								options[res.cart.items[i].item_id] = res.cart.items[i].options;
							}
						})
						.done(function() {
							var cartItems = [];
							res.cart.items.forEach(function(i) {
								var o = options[i.item_id];
								cartItems.push({
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
							juapp('cartItems', cartItems);
						})
						.fail(function (xhr, status, err) {
							var errorMessage = xhr.status + ': ' + status + '...' + err;
							alert('Error - ' + errorMessage);
						});
				};
				push();
				(function() {
					var _super = XMLHttpRequest.prototype.open;
					XMLHttpRequest.prototype.open = function() {
						for (var k in arguments) {
							try {
								var a = (typeof (arguments[k]) === 'string' ? arguments[k].toString() : false);
								if (a && (
									-1 < a.indexOf('?sections=cart') && -1 < a.indexOf('%2Cmessages')
									|| -1 < a.indexOf('?sections=cart') && -1 < a.indexOf('update_section_id')
								)) {
									this.addEventListener('load', push);
								}
							}
							catch(e) {console.log('justuno couldn\'t add the cart info');}
						}
						_super.apply(this, arguments);
					};
				})();
			}
		});
		require(['//cdn.justuno.com/vck.js'], function() {});
	});
});