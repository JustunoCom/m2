// 2019-11-15
define(['df', 'jquery', 'domReady!'], function(df, $) {return (
	/**
	 * @param {Object} config
	 * @param {String} config.id
	 */
	function(config) {
		/** @type {jQuery} HTMLButtonElement */ var $e = $(document.getElementById(config.id));
		$e.click(function() {
			function randomString(length, chars) {
				var result = '';
				for (var i = length; i > 0; --i) result += chars[Math.floor(Math.random() * chars.length)];
				return result;
			}
			var rString = randomString(32, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
			var s = document.getElementById('justuno_settings_options_interface_token_key');
			s.value = rString;
		});
	}
);});