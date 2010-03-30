if(!utils) { var utils = new Object(); }

utils = {
	// datamembers
	debug: false,
	eof: true
}

/**
 * Object that contains some function related to cookies
 * 
 * @author	Tijs Verkoyen <tijs@netlash.com>
 */
utils.cookies = {
	/**
	 * Are cookies enabled?
	 * 
	 * @return	bool
	 */
	isEnabled: function() {
		// try to grab the property
		var cookiesEnabled = (navigator.cookieEnabled) ? true : false;
		
		// unknown property?
		if(typeof navigator.cookieEanbled == 'undefined' && !cookiesEnabled) {
			// try to set a cookie
			document.cookie = 'testcookie';
			cookiesEnabled = (document.cookie.indexOf('testcookie') != -1);
		}
		
		// return
		return cookiesEnabled;
	},
	// end
	eof: true
}

/**
 * Object that contains some functions related to forms
 * 
 * @author	Tijs Verkoyen <tijs@netlash.com>
 */
utils.form = {
	/**
	 * Is a checkbox checked?
	 * 
	 * @return	bool
	 * @param	object	element
	 */
	isChecked: function(element) {
		return ($('input[name="'+ element.attr('name') +'"]:checked').length >= 1);
	},
	/**
	 * Is the value inside the element a valid emailaddress
	 *
	 * @return	bool
	 * @param	object	element
	 */
	isEmail: function(element) {
		var regexp = /^[a-z0-9!#\$%&'*+-\/=?^_`{|}\.~]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i;
		return regexp.test(element.val());
	},
	/**
	 * Is the element filled
	 *
	 * @return	bool
	 * @param	object	element
	 */
	isFilled: function(element) {
		return (utils.string.trim(element.val()) != '');
	},
	/**
	 * Is the value inside the element a valid number
	 *
	 * @return	bool
	 * @param	object	element
	 */
	isNumber: function(element) {
		return (!isNaN(element.val()) && element.val() != '');
	},
	/**
	 * Is the value inside the element a valid URL
	 *
	 * @return	bool
	 * @param	object	element
	 */
	isURL: function(element) {
		var regexp = /^((http|ftp|https):\/{2})?(([0-9a-zA-Z_-]+\.)+[0-9a-zA-Z]+)((:[0-9]+)?)((\/([~0-9a-zA-Z\#%@\.\/_-]+)?(\?[0-9a-zA-Z%@\/&=_-]+)?)?)$/i;
		return regexp.test(element.val());
	},
	// end
	eof: true
}

/**
 * Object that contains some functions related to strings
 * 
 * @author	Tijs Verkoyen <tijs@netlash.com>
 */
utils.string = {
	/**
	 * Replace all occurences of one string into a string
	 * 
	 * @return	string
	 * @param	string value
	 * @param	string needle
	 * @param	string replacement
	 */
	replaceAll: function(value, needle, replacement) {
		if(value == undefined)	return '';
		return value.replace(new RegExp(needle, 'g'), replacement);
	},
	/**
	 * Strip whitespace from the beginning and end of a string
	 * 
	 * @return	string
	 * @param	string value
	 */
	trim: function(value) {
		if(value == undefined) return '';
		return value.replace(/^\s+|\s+$/g, '');
	},
	/**
	 * Urlise a string (cfr. SpoonFilter)
	 * 
	 * @return	string
	 * @param	string value
	 */
	urlise: function(value) {
		// allowed chars
		var allowedChars = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_', ' '];
		
		// to lowercase
		value = value.toLowerCase();
		
		// replace accents
		value = value.replace(/[\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5]/g, 'a');
		value = value.replace(/[\u00E7]/g, 'c');
		value = value.replace(/[\u00E8\u00E9\u00EA\u00EB]/g, 'e');
		value = value.replace(/[\u00EC\u00ED\u00EE\u00EF]/g, 'i');
		value = value.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8]/g, 'o');
		value = value.replace(/[\u00F9\u00FA\u00FB\u00FC]/g, 'u');
		value = value.replace(/[\u00FD\u00FF]/g, 'y');
		value = value.replace(/[\u00F1]/g, 'n');
		value = value.replace(/[\u0153]/g, 'oe');
		value = value.replace(/[\u00E6]/g, 'ae');
		value = value.replace(/[\u00DF]/g, 'ss');
		
		// init var
		var url = '';
		
		// loop characters
		for(i in value) {
			// replace @
			if(value.charAt(i) == '@') url += 'at';
			else if(value.charAt(i) == '©') url += 'copyright';
			else if(value.charAt(i) == '€') url += 'euro';
			else if(value.charAt(i) == '™') url += 'tm';
			else if(value.charAt(i) == '-') url += ' ';

			// only append chars that are allowed
			else if(allowedChars.indexOf(value.charAt(i)) != -1) url += value.charAt(i);
		}

		// trim
		url = utils.string.trim(url);

		// replace double dashes
		url = url.replace(/\s+/g, ' ');
				
		// replace spaces with dashes
		url = utils.string.replaceAll(url, ' ', '-');
		
		// trim		
		return url;
	},
	// end
	eof: true
}