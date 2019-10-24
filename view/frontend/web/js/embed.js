/* eslint-disable no-console */
// eslint-disable-next-line no-undef
/* eslint-disable camelcase */
// eslint-disable-next-line no-undef
define([], function () {
	'use strict';
	// eslint-disable-next-line no-unused-vars
	return function (config, element) {
		window.console.log(config.message);
		window.asset_host = '//cdn.justuno.com/';
		(function (i, s, o, g, r, a, m) {
			i[r] = i[r] || function () {
				(i[r].q = i[r].q || []).push(arguments);
			};
			a = s.createElement(o);
			m = s.getElementsByTagName(o)[0];
			a.async = 1;
			a.src = g;
			m.parentNode.insertBefore(a, m);
			// eslint-disable-next-line no-undef
		})(window, document, 'script', asset_host + 'vck.js', 'juapp');
	};
});