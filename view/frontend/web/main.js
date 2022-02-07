// 2019-11-15
define([], function () {
	return (
		/**
		 * @param {Object} cfg
		 * @param {String} cfg.merchantId
		 * ju_is_catalog_product_view():
		 * @param {?String} cfg.action
		 * @param {?Number} cfg.productId
		 * ju_is_checkout_success():
		 * @param {?Number} cfg.orderId
		 * @param {?Object} cfg.order
		 */
		function (cfg) {
			window.ju_num = cfg.merchantId;
			window.console.log(`ju_num loaded (${cfg.merchantId})`);
			(function () {
				var k = "juapp";
				window[k] =
					window[k] ||
					function () {
						(window[k].q = window[k].q || []).push(arguments);
					};
			})();
			juapp("initFunc", function () {
				if (cfg.order) {
					// Changing this from juapp("order", cfg.orderId, cfg.order);
					// to juapp("order",  cfg.order); that reflects our latest
					// structure
					juapp("order", cfg.order);
				} else
					require([
						"ju-lodash",
						"Magento_Customer/js/customer-data",
					], function (_, cd) {
						if (cfg.productId) {
							juapp("local", "pageType", cfg.action);
							juapp("local", "prodId", cfg.productId);
							(function () {
								var customer = cd.get("customer");
								var updateCustomer = function () {
									juapp("local", "custId", customer().id);
								};
								updateCustomer();
								customer.subscribe(updateCustomer, this);
							})();
						}
						(function () {
							var cart = cd.get("cart");
							var updateCart = function () {
								var oVal = function (oo, l) {
									return (
										(
											_.find(oo, function (o) {
												return (
													l ===
													o["label"].toLowerCase()
												);
											}) || {}
										).value || null
									);
								};
								/**
								 * 2022-02-01
								 * «As per requirement we need to send cart information as well.».
								 */
								juapp("cart", {
									total: cart().total,
									subtotal: cart().subtotal,
									tax: cart().tax,
									shipping: cart().shipping,
									currency: cart().currency,
								});
								/**
								 * 2019-11-16
								 * «This function will essentially replace the current Justuno tracked cart items
								 * with the array you provide».
								 * https://support.justuno.com/tracking-visitor-carts-conversions-past-orders
								 */
								juapp(
									"cartItems",
									_.map(cart().items, function (i) {
										return {
											color: oVal(i.options, "color"),
											name: i["product_name"],
											price: i["product_price_value"],
											productID: i["product_id"],
											qty: i["qty"],
											size: oVal(i.options, "size"),
											sku: i["product_sku"],
											variationID: i["item_id"],
										};
									})
								);
							};
							updateCart();
							cart.subscribe(updateCart, this);
						})();
					});
			});
			// 2020-01-24 "Replace `cdn.justuno.com` with `cdn.jst.ai`": https://github.com/justuno-com/m2/issues/8
			// Change here and place magento's EmbedCode link
			// https://storage.googleapis.com/justuno-shopify-embed/5d435919d7724cc990df63301ced3b5d/shopifyEmbed.js?shop=bitsprojustuno.myshopify.com
			require([
				`//storage.googleapis.com/justuno-shopify-embed/${cfg.merchantId}/magentoEmbed.js`,
			], function () {});
		}
	);
});
