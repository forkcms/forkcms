/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	function URI(u, s) {
		var t = this, o, a, b;

		// Default settings
		s = t.settings = s || {};

		// Strange app protocol or local anchor
		if (/^(mailto|news|javascript|about):/i.test(u) || /^\s*#/.test(u)) {
			t.source = u;
			return;
		}

		// Absolute path with no host, fake host and protocol
		if (u.indexOf('/') === 0 && u.indexOf('//') !== 0)
			u = (s.base_uri ? s.base_uri.protocol || 'http' : 'http') + '://mce_host' + u;

		// Relative path
		if (u.indexOf(':/') === -1 && u.indexOf('//') !== 0)
			u = (s.base_uri.protocol || 'http') + '://mce_host' + t.toAbsPath(s.base_uri.path, u);

		// Parse URL (Credits goes to Steave, http://blog.stevenlevithan.com/archives/parseuri)
		u = u.replace(/@@/g, '(mce_at)'); // Zope 3 workaround, they use @@something
		u = /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/.exec(u);
		$(["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","anchor"]).each(function(i, v) {
			var s = u[i];

			// Zope 3 workaround, they use @@something
			if (s)
				s = s.replace(/\(mce_at\)/g, '@@');

			t[v] = s;
		});

		if (b = s.base_uri) {
			if (!t.protocol)
				t.protocol = b.protocol;

			if (!t.userInfo)
				t.userInfo = b.userInfo;

			if (!t.port && t.host == 'mce_host')
				t.port = b.port;

			if (!t.host || t.host == 'mce_host')
				t.host = b.host;

			t.source = '';
		}
	};

	$.extend(URI.prototype, {
		/**
		 * Sets the internal path part of the URI.
		 *
		 * @param {string} p Path string to set.
		 */
		setPath : function(p) {
			var t = this;

			p = /^(.*?)\/?(\w+)?$/.exec(p);

			// Update path parts
			t.path = p[0];
			t.directory = p[1];
			t.file = p[2];

			// Rebuild source
			t.source = '';
			t.getURI();
		},

		/**
		 * Converts the specified URI into a relative URI based on the current URI instance location.
		 *
		 * @param {String} u URI to convert into a relative path/URI.
		 * @return {String} Relative URI from the point specified in the current URI instance.
		 */
		toRelative : function(u) {
			var t = this, o;

			if (u === "./")
				return u;

			u = new URI(u, {base_uri : t});

			// Not on same domain/port or protocol
			if ((u.host != 'mce_host' && t.host != u.host && u.host) || t.port != u.port || t.protocol != u.protocol)
				return u.getURI();

			o = t.toRelPath(t.path, u.path);

			// Add query
			if (u.query)
				o += '?' + u.query;

			// Add anchor
			if (u.anchor)
				o += '#' + u.anchor;

			return o;
		},
	
		/**
		 * Converts the specified URI into a absolute URI based on the current URI instance location.
		 *
		 * @param {String} u URI to convert into a relative path/URI.
		 * @param {bool} nh No host and protocol prefix.
		 * @return {String} Absolute URI from the point specified in the current URI instance.
		 */
		toAbsolute : function(u, nh) {
			var u = new URI(u, {base_uri : this});

			return u.getURI(this.host == u.host ? nh : 0);
		},

		/**
		 * Converts a absolute path into a relative path.
		 *
		 * @param {String} base Base point to convert the path from.
		 * @param {String} path Absolute path to convert into a relative path.
		 */
		toRelPath : function(base, path) {
			var items, bp = 0, out = '', i, l;

			// Split the paths
			base = base.substring(0, base.lastIndexOf('/'));
			base = base.split('/');
			items = path.split('/');

			if (base.length >= items.length) {
				for (i = 0, l = base.length; i < l; i++) {
					if (i >= items.length || base[i] != items[i]) {
						bp = i + 1;
						break;
					}
				}
			}

			if (base.length < items.length) {
				for (i = 0, l = items.length; i < l; i++) {
					if (i >= base.length || base[i] != items[i]) {
						bp = i + 1;
						break;
					}
				}
			}

			if (bp == 1)
				return path;

			for (i = 0, l = base.length - (bp - 1); i < l; i++)
				out += "../";

			for (i = bp - 1, l = items.length; i < l; i++) {
				if (i != bp - 1)
					out += "/" + items[i];
				else
					out += items[i];
			}

			return out;
		},

		/**
		 * Converts a relative path into a absolute path.
		 *
		 * @param {String} base Base point to convert the path from.
		 * @param {String} path Relative path to convert into an absolute path.
		 */
		toAbsPath : function(base, path) {
			var i, nb = 0, o = [];

			// Split paths
			base = base.split('/');
			path = path.split('/');

			// Remove empty chunks
			$(base).each(function(i, k) {
				if (k)
					o.push(k);
			});

			base = o;

			// Merge relURLParts chunks
			for (i = path.length - 1, o = []; i >= 0; i--) {
				// Ignore empty or .
				if (path[i].length == 0 || path[i] == ".")
					continue;

				// Is parent
				if (path[i] == '..') {
					nb++;
					continue;
				}

				// Move up
				if (nb > 0) {
					nb--;
					continue;
				}

				o.push(path[i]);
			}

			i = base.length - nb;

			// If /a/b/c or /
			if (i <= 0)
				return '/' + o.reverse().join('/');

			return '/' + base.slice(0, i).join('/') + '/' + o.reverse().join('/');
		},

		/**
		 * Returns the full URI of the internal structure.
		 *
		 * @param {bool} nh Optional no host and protocol part. Defaults to false.
		 */
		getURI : function(nh) {
			var s, t = this;

			// Rebuild source
			if (!t.source || nh) {
				s = '';

				if (!nh) {
					if (t.protocol)
						s += t.protocol + '://';

					if (t.userInfo)
						s += t.userInfo + '@';

					if (t.host)
						s += t.host;

					if (t.port)
						s += ':' + t.port;
				}

				if (t.path)
					s += t.path;

				if (t.query)
					s += '?' + t.query;

				if (t.anchor)
					s += '#' + t.anchor;

				t.source = s;
			}

			return t.source;
		}
	});

	$.parseURI = function(u, s) {
		s = s || {};

		return new URI(u, $.extend({base_uri : new URI(s.base_url || document.location.href)}, s));
	};
})(jQuery);