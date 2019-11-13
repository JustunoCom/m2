define(['jquery',], function ($)
{
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

		//Frontend API cart interface with preparsed cart item json, noteable 'options' indexing
		window.justunoDataSync = function () {
			$.ajax({
				url: '/customer/section/load/?sections=cart',
				type: 'GET',
				dataType: 'text',
			}).done(function (data) {
				window.ju_MageCheckout = JSON.parse(data);
				window.ju_MageCart = window.ju_MageCheckout.cart;
				Object.values(window.ju_MageCart.items).forEach(function (item) {
					return (item.options = JSON.parse(
						item.options.map(function (obj) {
							var rObj = {};
							rObj[obj.label] = JSON.parse(JSON.stringify(window.omitKeys(obj, ['label'])));
							return JSON.stringify(rObj);
						})
					));
				});
				window.ju_MageItems = window.ju_MageCart.items;
				typeof window.ju_MageCartOptInterface == 'undefined' ? window.ju_MageCartOptInterface = {} : console.log('initializing options interface');
				for (var i = 0; i < Object.values(window.ju_MageItems).length; i++) {
					window.ju_MageCartOptInterface[window.ju_MageItems[i].item_id] = window.ju_MageItems[i].options;
				}
			}).fail(function (xhr, status, err) {
				//Ajax request failed.
				var errorMessage = xhr.status + ': ' + status + '...' + err;
				alert('Error - ' + errorMessage);
			});
		};

		window.justunoDataSync();
		juapp('initFunc',
			function() {
				setTimeout(function(){
					if(!window.ju_order_obj) {
						window.juapp('cartItems', window.ju_sync_code);
					}
					if(window.ju_order_obj) {
						window.juapp('order', window.ju_order_id, window.ju_order_obj);
					}
					window.console.log(config.message);
				},1000);
			}
		);


		window.justunoDataPush = function() {
			window.justunoDataSync();
			setTimeout(function(){
				for (var i = 0; i < Object.values(window.ju_MageItems).length; i++) {
					window.ju_sync_code[i] = {
						"productid":window.ju_MageItems[i].product_id,
						"variationid":window.ju_MageItems[i].item_id,
						"sku":window.ju_MageItems[i].product_sku,
						"quantity":window.ju_MageItems[i].qty,
						"price":window.ju_MageItems[i].product_price_value,
						"name":window.ju_MageItems[i].product_name,
						"color":window.ju_MageCartOptInterface[window.ju_MageItems[i].item_id].Color ? window.ju_MageCartOptInterface[window.ju_MageItems[i].item_id].Color.value : window.ju_MageCartOptInterface[window.ju_MageItems[i].item_id].color ? window.ju_MageCartOptInterface[window.ju_MageItems[i].item_id].color.value : null,
						"size":window.ju_MageCartOptInterface[window.ju_MageItems[i].item_id].Size ? window.ju_MageCartOptInterface[window.ju_MageItems[i].item_id].Size.value : window.ju_MageCartOptInterface[window.ju_MageItems[i].item_id].size ? window.ju_MageCartOptInterface[window.ju_MageItems[i].item_id].size.value : null
					};
				}
				window.juapp('cartItems', window.ju_sync_code);
			},1000);

		};

		var origOpen = XMLHttpRequest.prototype.open;
		XMLHttpRequest.prototype.open = function () {
			for (var arg in arguments) {
				try {
					var thisarg = (typeof (arguments[arg]) === 'string' ? arguments[arg].toString() : false);
					if (thisarg) {
						if (thisarg.indexOf('?sections=cart%2Cmessages') >= 0) {
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
	};
});
