// 2019-11-15
define(['jquery', 'domReady!'], function($) {return (
	/**
	 * @param {Object} config
	 * @param {String} config.id
	 */
	function(config) {
		/** @type {jQuery} HTMLButtonElement */ var $e = $(document.getElementById(config.id));
		$e.click(function() {
			function randomString(length, chars) {
				var r = '';
				for (var i = length; i > 0; --i) {
					r += chars[Math.floor(Math.random() * chars.length)];
				}
				return r;
			}
			document.getElementById('justuno_settings_options_interface_token_key').value = randomString(
				32, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
			);
		});
	}
);});