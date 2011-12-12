/**
 * Utilities; useful scripts
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
var utils =
{
	// datamembers
	debug: false
}

/**
 * Functions related to arrays
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
utils.array =
{
	/**
	 * Is the given value present in the array
	 *
	 * @return	bool
	 */
	inArray: function(needle, array)
	{
		// loop values
		for(var i in array)
		{
			if(array[i] == needle) return true;
		}

		// fallback
		return false;
	}
}

/**
 * Function related to cookies
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
utils.cookies =
{
	/**
	 * Are cookies enabled?
	 *
	 * @return	bool
	 */
	isEnabled: function()
	{
		// try to grab the property
		var cookiesEnabled = (navigator.cookieEnabled) ? true : false;

		// unknown property?
		if(typeof navigator.cookieEnabled == 'undefined' && !cookiesEnabled)
		{
			// try to set a cookie
			document.cookie = 'testcookie';
			cookiesEnabled = ($.inArray('testcookie', document.cookie) != -1);
		}

		// return
		return cookiesEnabled;
	},

	/**
	 * Read a cookie
	 *
	 * @return	mixed
	 */
	readCookie: function(name)
	{
		// get cookies
		var cookies = document.cookie.split(';');
		name = name + '=';

		for(var i = 0; i < cookies.length; i++)
		{
			var cookie = cookies[i];
			while(cookie.charAt(0) == ' ') cookie = cookie.substring(1, cookie.length);
			if(cookie.indexOf(name) == 0) return cookie.substring(name.length, cookie.length);
		}

		// fallback
		return null;
	}
}

/**
 * Functions related to forms
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
utils.form =
{
	/**
	 * Is a checkbox checked?
	 *
	 * @return	bool
	 * @param	object element
	 */
	isChecked: function(element)
	{
		return ($('input[name="' + element.attr('name') + '"]:checked').length >= 1);
	},

	/**
	 * Is the value inside the element a valid emailaddress
	 *
	 * @return	bool
	 * @param	object element
	 */
	isEmail: function(element)
	{
		var regexp = /^[a-z0-9!#\$%&'*+-\/=?^_`{|}\.~]+@([a-z0-9]+([\-]+[a-z0-9]+)*\.)+[a-z]{2,7}$/i;
		return regexp.test(element.val());
	},

	/**
	 * Is the element filled
	 *
	 * @return	bool
	 * @param	object element
	 */
	isFilled: function(element)
	{
		return (utils.string.trim(element.val()) != '');
	},

	/**
	 * Is the value inside the element a valid number
	 *
	 * @return	bool
	 * @param	object element
	 */
	isNumber: function(element)
	{
		return (!isNaN(element.val()) && element.val() != '');
	},

	/**
	 * Is the value inside the element a valid URL
	 *
	 * @return	bool
	 * @param	object element
	 */
	isURL: function(element)
	{
		var regexp = /^((http|ftp|https):\/{2})?(([0-9a-zA-Z_-]+\.)+[0-9a-zA-Z]+)((:[0-9]+)?)((\/([~0-9a-zA-Z\#%@\.\/_-]+)?(\?[0-9a-zA-Z%@\/&=_-]+)?)?)$/i;
		return regexp.test(element.val());
	}
},

/**
 * Functions related to strings
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Dieter Vanden Eynde <dieter@netlash.com>
 * @author	Matthias Mullie <matthias@mullie.eu>
 */
utils.string =
{
	// data member
	div: false,

	/**
	 * Fix a HTML5-chunk, so IE can render it
	 *
	 * @return	string
	 * @param	string html
	 */
	html5: function(html)
	{
		var html5 = 'abbr article aside audio canvas datalist details figcaption figure footer header hgroup mark meter nav output progress section summary time video'.split(' ');

		// create div if needed
		if(utils.string.div === false)
		{
			utils.string.div = document.createElement('div');

			utils.string.div.innerHTML = '<nav></nav>';

			if(utils.string.div.childNodes.length !== 1)
			{
				var fragment = document.createDocumentFragment();
				var i = html5.length;
				while(i--) fragment.createElement(html5[i]);

				fragment.appendChild(utils.string.div);
			}
		}

		html = html.replace(/^\s\s*/, '').replace(/\s\s*$/, '')
					.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');

		// fix for when in a table
		var inTable = html.match(/^<(tbody|tr|td|th|col|colgroup|thead|tfoot)[\s\/>]/i);

		if(inTable) utils.string.div.innerHTML = '<table>' + html + '</table>';
		else utils.string.div.innerHTML = html;

		var scope;
		if(inTable) scope = utils.string.div.getElementsByTagName(inTable[1])[0].parentNode;
		else scope = utils.string.div;

		var returnedFragment = document.createDocumentFragment();
		var i = scope.childNodes.length;
		while(i--) returnedFragment.appendChild(scope.firstChild);

		return returnedFragment;
	},

	/**
	 * Encode the string as HTML
	 *
	 * @return	string
	 * @param	string value
	 */
	htmlEncode: function(value)
	{
		return $('<div/>').text(value).html();
	},

	/**
	 * Decode the string as HTML
	 *
	 * @return	string
	 * @param	string value
	 */
	htmlDecode: function(value)
	{
		return $('<div/>').html(value).text();
	},

	/**
	 * Replace all occurences of one string into a string
	 *
	 * @return	string
	 * @param	string value
	 * @param	string needle
	 * @param	string replacement
	 */
	replaceAll: function(value, needle, replacement)
	{
		if(value == undefined) return '';
		return value.replace(new RegExp(needle, 'g'), replacement);
	},

	/**
	 * Strip whitespace from the beginning and end of a string
	 *
	 * @return	string
	 * @param	string value
	 * @param	string[optional] charlist
	 */
	trim: function(value, charlist)
	{
		if(value == undefined) return '';
		if(charlist == undefined) charlist = ' ';

		var pattern = new RegExp('^[' + charlist + ']*|[' + charlist + ']*$', 'g');
		return value.replace(pattern, '');
	},

	/**
	 * PHP-like urlencode
	 *
	 * @see		https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Global_Functions/encodeURIComponent#Description
	 * @return	string
	 * @param	string value
	 */
	urlEncode: function(value)
	{
		return encodeURIComponent(value).replace(/\%20/g, '+').replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A').replace(/\~/g, '%7E');
	},

	/**
	 * PHP-like urlencode
	 *
	 * @see		https://developer.mozilla.org/en/Core_JavaScript_1.5_Reference/Global_Functions/encodeURIComponent#Description
	 * @return	string
	 * @param	string value
	 */
	urlDecode: function(value)
	{
		return decodeURIComponent(value.replace(/\+/g, '%20').replace(/\%21/g, '!').replace(/\%27/g, "'").replace(/\%28/g, '(').replace(/\%29/g, ')').replace(/\%2A/g, '*').replace(/\%7E/g, '~'));
	},

	/**
	 * Urlise a string (cfr. SpoonFilter::urlise)
	 *
	 * @return	string
	 * @param	string value
	 */
	urlise: function(value)
	{
		// reserved characters (RFC 3986)
		reservedCharacters = new Array(
			'/', '?', ':', '@', '#', '[', ']',
			'!', '$', '&', '\'', '(', ')', '*',
			'+', ',', ';', '='
		);

		// remove reserved characters
		for(i in reservedCharacters) value = value.replace(reservedCharacters[i], ' ');

		// replace double quote, since this one might cause problems in html (e.g. <a href="double"quote">)
		value = utils.string.replaceAll(value, '"', ' ');

		// replace spaces by dashes
		value = utils.string.replaceAll(value, ' ', '-');

		// only urlencode if not yet urlencoded
		if(utils.string.urlDecode(value) == value)
		{
			// to lowercase
			value = value.toLowerCase();

			// urlencode
			value = utils.string.urlEncode(value);
		}

		// convert "--" to "-"
		value = value.replace(/-+/, '-');

		// trim - signs
		return utils.string.trim(value, '-');
	},

	/**
	 * Convert a HTML string to a XHTML string.
	 *
	 * @return	string
	 * @param	string value
	 */
	xhtml: function(value)
	{
		// break tags should end with a slash
		value = value.replace(/<br>/g,'<br />');
		value = value.replace(/<br ?\/?>$/g,'');
		value = value.replace(/^<br ?\/?>/g,'');

		// image tags should end with a slash
		value = value.replace(/(<img [^>]+[^\/])>/gi,'$1 />');

		// input tags should end with a slash
		value = value.replace(/(<input [^>]+[^\/])>/gi,'$1 />');

		// big no-no to <b|i|u>
		value = value.replace(/<b\b[^>]*>(.*?)<\/b[^>]*>/g,'<strong>$1</strong>');
		value = value.replace(/<i\b[^>]*>(.*?)<\/i[^>]*>/g,'<em>$1</em>');
		value = value.replace(/<u\b[^>]*>(.*?)<\/u[^>]*>/g,'<span style="text-decoration:underline">$1</span>');

		// XHTML
		return value;
	}
}

/**
 * Functions related to the current url
 *
 * @author	Dieter Vanden Eynde <dieter@netlash.com>
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
utils.url =
{
	extractParamFromUri: function (uri, paramName)
	{
		if(!uri) return;
		var uri = uri.split('#')[0];
		var parts = uri.split('?');
		if (parts.length == 1) return;

		var query = decodeURI(parts[1]);

		paramName += '=';
		var params = query.split('&');
		for(var i=0, param; param = params[i]; ++i)
		{
			if(param.indexOf(paramName) === 0) return unescape(param.split('=')[1]);
		}
	},

	/**
	 * Get a GET parameter
	 *
	 * @return	string
	 * @param	string name
	 */
	getGetValue: function(name)
	{
		// init return value
		var getValue = '';

		// get GET chunks from url
		var hashes = window.location.search.slice(window.location.search.indexOf('?') + 1).split('&');

		// find requested parameter
		$.each(hashes, function(index, value)
		{
			// split name/value up
			var chunks = value.split('=');

			// found the requested parameter
			if(chunks[0] == name)
			{
				// set for return
				getValue = chunks[1];

				// break loop
				return false;
			}
		});

		// cough up value
		return getValue;
	}
}