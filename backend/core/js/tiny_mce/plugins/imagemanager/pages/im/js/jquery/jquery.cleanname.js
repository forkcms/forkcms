/**
 * Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function($){
	$.cleanName = function(s) {
		var i, lo;

		// Replace diacritics
		lo = [
			/[\300-\306]/g, 'A', /[\340-\346]/g, 'a',
			/\307/g, 'C', /\347/g, 'c',
			/[\310-\313]/g, 'E', /[\350-\353]/g, 'e',
			/[\314-\317]/g, 'I', /[\354-\357]/g, 'i',
			/\321/g, 'N', /\361/g, 'n',
			/[\322-\330]/g, 'O', /[\362-\370]/g, 'o',
			/[\331-\334]/g, 'U', /[\371-\374]/g, 'u'
		];

		for (i = 0; i < lo.length; i += 2)
			s = s.replace(lo[i], lo[i + 1]);

		// Replace whitespace
		s = s.replace(/\s+/g, '_');

		// Remove anything else
		s = s.replace(/[^a-z0-9_\-\.]+/gi, '');

		return s;
	};
})(jQuery);