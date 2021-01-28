// 2019-11-15
define(['jquery', 'domReady!'], function($) {return (
	/**
	 * @param {Object} config
	 * @param {String} config.id
	 */
	function(config) {
		/** @type {jQuery} HTMLButtonElement */ var $e = $(document.getElementById(config.id));
		$e.click(function() {
			document.getElementById('justuno_settings_options_interface_token_key').value = function() {
				var r = '';
				const c = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				const cl = c.length;
				for (let i = 32; 0 < i; i--) {
					r += c[Math.floor(cl * Math.random())];
				}
				return r;
			}();
		});
	}
);});