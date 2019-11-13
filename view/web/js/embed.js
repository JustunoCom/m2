define([], function () {
	'use strict';
	return function (config, element) {
		window.console.log(config.message +' fssdfdsf');
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
		})(window, document, 'script', asset_host + 'vck.js', 'juapp');
	};
});
