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
				var push = function() {
					$
						.ajax({dataType: 'json', type: 'GET', url: '/customer/section/load/?sections=cart'})
						.then(function(res) {
							var omitKeys = function(obj, keys) {
								var dup = {};
								for (var k in obj) {
									if (keys.indexOf(k) == -1) {
										dup[k] = obj[k];
									}
								}
								return dup;
							};
							Object.values(res.cart.items).forEach(function(item) {
								return (item.options =
									item.options.map(function (obj) {
										var rObj = {};
										rObj[obj.label] = JSON.parse(JSON.stringify(omitKeys(obj, ['label'])));
										return JSON.stringify(rObj);
									})
								);
							});
							if ('undefined' === typeof window.ju_MageCartOptInterface){
								window.ju_MageCartOptInterface = {};
							}
							for (var i = 0; i < Object.values(res.cart.items).length; i++) {
								window.ju_MageCartOptInterface[res.cart.items[i].item_id] = res.cart.items[i].options;
							}
						})
						.done(function() {
							var ju_cart_obj = [];
							res.cart.items.forEach(function(item) {
								var itemId = item.product_id;
								var itemVariantId = item.item_id;
								var itemSku = item.product_sku;
								var itemTitle = item.product_name;
								var itemPrice = item.product_price_value;
								var itemQty = item.qty;
								var itemColor = window.ju_MageCartOptInterface[item.item_id].Color ? window.ju_MageCartOptInterface[item.item_id].Color.value : window.ju_MageCartOptInterface[item.item_id].color ? window.ju_MageCartOptInterface[item.item_id].color.value : null;
								var itemSize = window.ju_MageCartOptInterface[item.item_id].Size ? window.ju_MageCartOptInterface[item.item_id].Size.value : window.ju_MageCartOptInterface[item.item_id].size ? window.ju_MageCartOptInterface[item.item_id].size.value : null;
								ju_cart_obj.push({ productid: itemId, variationid: itemVariantId, sku: itemSku, quantity: itemQty, price: itemPrice, name: itemTitle, color: itemColor, size: itemSize });
							});
							window.ju_sync_code = ju_cart_obj;
							window.juapp('cartItems', ju_cart_obj);
							console.log('ju_sync_code generated');
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