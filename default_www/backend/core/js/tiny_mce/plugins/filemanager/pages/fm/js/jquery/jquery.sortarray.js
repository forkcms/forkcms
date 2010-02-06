/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	$.fn.sortArray = function(by, desc) {
		var data = $.makeArray(this);

		function numSort(a, b) {
			a = parseFloat(a[by]);
			b = parseFloat(b[by]);

			a = isNaN(a) ? 0 : a;
			b = isNaN(b) ? 0 : b;

			return desc ? b - a : a - b;
		};

		function strSort(a, b) {
			try {
				a = '' + a[by].toLowerCase();
				b = '' + b[by].toLowerCase();

				if (a == b)
					return 0;

				if (desc ? a > b : a < b)
					return -1;
			} catch (ex) {
				// Ignore
			}

			return 1;
		};

		if (data.length)
			data = data.sort(typeof(data[0][by]) == 'number' ? numSort : strSort);

		return data;
	};

})(jQuery);