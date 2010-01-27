if(!utils) { var utils = new Object(); }

utils = {
	// datamembers
	debug: true,
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
	 * @todo	fix
	 * @return	bool
	 * @param	object	element
	 */
	isEmail: function(element) {
		var regexp = /^[a-z0-9_\.-]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i;
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
	 * @todo	fix
	 * @return	bool
	 * @param	object	element
	 */
	isURL: function(element) {
		var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/i;
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
	 * @todo	fix
	 * @return	string
	 * @param	string value
	 */
	urlise: function(value) {
		return utils.string.replaceAll(value, ' ', '-');
	},
	// end
	eof: true
}