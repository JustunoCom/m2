define([], function () {
	'use strict';
	return function (config, element) {
		window.omitKeys = function (obj, keys) {
			var dup = {};
			for (var key in obj) {
				if (keys.indexOf(key) == -1) {
					dup[key] = obj[key];
				}
			}
			return dup;
		};
		juapp('initFunc',
			function() {
				window.justunoDataPush = function() {
					jju.ajax({
						url: '/customer/section/load/?sections=cart',
						type: 'GET',
						dataType: 'json',
					}).then(function (data) {
						// console.log(data + '--data');
						window.ju_MageCheckout = data;
						window.ju_MageCart = window.ju_MageCheckout.cart;
						Object.values(window.ju_MageCart.items).forEach(function (item) {
							return (item.options = 
								item.options.map(function (obj) {
									var rObj = {};
									rObj[obj.label] = JSON.parse(JSON.stringify(window.omitKeys(obj, ['label'])));
									return JSON.stringify(rObj);
								})
							);
						});
						window.ju_MageItems = window.ju_MageCart.items;
						if(typeof window.ju_MageCartOptInterface == 'undefined'){
							window.ju_MageCartOptInterface = {};
						}
						for (var i = 0; i < Object.values(window.ju_MageItems).length; i++) {
							window.ju_MageCartOptInterface[window.ju_MageItems[i].item_id] = window.ju_MageItems[i].options;
						}
					}).done(function () {
						var ju_cart_obj = [];
						if(window.ju_MageItems.length !== 0) {
							window.ju_MageItems.forEach(function(item){
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
						}
						else {
							window.ju_sync_code = ju_cart_obj;
							window.juapp('cartItems', ju_cart_obj);
							console.log('ju_sync_code generated');
						}
					}).fail(function (xhr, status, err) {
						//Ajax request failed.
						var errorMessage = xhr.status + ': ' + status + '...' + err;
						alert('Error - ' + errorMessage);
					});
				};
				if(window.ju_MageProductView) {
					juapp('local','pageType',ju_MageProductView.PageID);
					juapp('local','prodId',ju_MageProductView.ProductID);
					juapp('local','custId',ju_MageProductView.CustomerID);
					console.log('product view logged' + ' - ' + ju_MageProductView.CustomerID);
				}

				if(!window.ju_order_obj) {
					window.justunoDataPush();
					var origOpen = XMLHttpRequest.prototype.open;
					XMLHttpRequest.prototype.open = function () {
						for (var arg in arguments) {
							try {
								var thisarg = (typeof (arguments[arg]) === 'string' ? arguments[arg].toString() : false);
								if (thisarg) {
									if ((thisarg.indexOf('?sections=cart') >= 0 && thisarg.indexOf('%2Cmessages') >= 0) || (thisarg.indexOf('?sections=cart') >= 0 && thisarg.indexOf('update_section_id') >= 0)) {
										this.addEventListener('load', function() {
											window.justunoDataPush();
										});
									}
									else if (window.ju_MageAJAX && thisarg.indexOf(window.ju_MageAJAX) >= 0) {
										this.addEventListener('load', function() {
											window.justunoDataPush();
										});
									}
								}
							} catch (e) {
								console.log('justuno couldn\'t add the cart info');
							}
						}
						origOpen.apply(this, arguments);
					};
				}
				if(window.ju_order_obj) {
					window.juapp('order', window.ju_order_id, window.ju_order_obj);
				}
			}
		);
	};
});