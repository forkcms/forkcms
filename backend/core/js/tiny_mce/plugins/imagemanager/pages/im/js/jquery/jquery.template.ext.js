/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($){
	$.template.regx.standard = $.template.regx.ext;

	$.templateFromScript = function(el, opts) {
		return $.template($.trim($.translate($(el).html().replace(/(<!\[CDATA\[|\]\]>)/gi, ''))), opts);
	};

	// Add template helpers
	$.extend($.template.helpers, {
		sizefix : function(v) {
			v = parseInt(v);

			if (isNaN(v) || v == -1)
				return '';

			// MB
			if (v > 1048576)
				return Math.round(v / 1048576, 1) + " MB";

			// KB
			if (v > 1024)
				return Math.round(v / 1024, 1) + " KB";

			return v + " b";
		},

		encodeURIComponent : function(v) {
			return encodeURIComponent(v);
		}
	});
})(jQuery);