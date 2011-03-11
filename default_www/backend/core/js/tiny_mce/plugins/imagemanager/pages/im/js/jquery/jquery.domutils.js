/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	$.extend({
		createElm : function(n, a, h) {
			n = $(document.createElement(n));

			n.attr(a).html(h);

			return n;
		},

		appendElements : function(te, ne) {
			var i, n;

			if (typeof(ne) == 'string')
				te.appendChild(document.createTextNode(ne));
			else if (ne.length) {
				te = te.appendChild($.createElm(ne[0], ne[1])[0]);

				for (i=2; i<ne.length; i++)
					$.appendElements(te, ne[i]);
			}
		},

		scrollPos : function() {
			var w = window, b = document.body;

			return {
				x : w.pageXOffset || b.scrollLeft,
				y : w.pageYOffset || b.scrollTop
			};
		},

		winWidth : function() {
			return window.innerWidth || $(window).width();
		},

		winHeight : function() {
			return window.innerHeight || $(window).height();
		}
	});

	$.fn.extend({
		appendAll : function(ne) {
			this.each(function(i, v) {
				$.appendElements(v, ne);
			});
		}
	});
})(jQuery);