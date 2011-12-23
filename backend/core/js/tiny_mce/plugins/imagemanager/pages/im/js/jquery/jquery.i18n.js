/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	function xmlEncode(s) {
		return s ? ('' + s).replace(new RegExp('[<>&"\']', 'g'), function (c, b) {
			switch (c) {
				case '&':
					return '&amp;';

				case '"':
					return '&quot;';

				case '\'':
					return '&#39;'; // &apos; is not working in MSIE

				case '<':
					return '&lt;';

				case '>':
					return '&gt;';
			}

			return c;
		}) : s;
	};

	$.translate = function(s, e, va) {
		return s.replace(/\{#([^\}]+)\}/g, function(a, b) {
			var pa = b.split(/\./);

			if (MCManagerI18n[pa[0]])
				a = MCManagerI18n[pa[0]][pa[1]];

			a = a ? a : '{#' + b + '}';

			if (va) {
				a = a.replace(/\{([^\}]+)\}/g, function(a, b) {
					return va[b] || a;
				});
			}

			return e ? xmlEncode(a) : a;
		});
	};

	$.translateElement = function(e) {
		e = e || document.body;
		e.innerHTML = $.translate(e.innerHTML.replace(/=({#[a-z0-9_]+})/gi, '="$1"'), 1);
	};

	$(function() {
		$.extend($.WindowManager.i18n, {
			yes : MCManagerI18n['common']['confirm_yes'],
			no : MCManagerI18n['common']['confirm_no'],
			ok : MCManagerI18n['common']['alert_ok']
		});
	}); 

	document.title = $.translate(document.title.replace(/^(http|https):\/\/[a-z0-9._\-\s]+-/gi, ''));

	$('body > *').each(function(i, v) {
		if (!/(SCRIPT|BR|HR|INPUT|META|IMG|LINK|PARAM|IFRAME)/.test(v.nodeName))
			$.translateElement(v);
	});
})(jQuery);