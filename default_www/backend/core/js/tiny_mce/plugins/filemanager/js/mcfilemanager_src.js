/**
 * $Id: mcfilemanager_src.js 723 2009-09-09 13:24:16Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	window.mcFileManager = {
		settings : {
			document_base_url : '',
			relative_urls : false,
			remove_script_host : false,
			use_url_path : true,
			remember_last_path : 'auto',
			target_elements : '',
			target_form : '',
			handle : 'file'
		},

		setup : function() {
			var t = this, o, d = document, cp = [];

			// Find document_base_url
			o = d.location.href;

			if (o.indexOf('?') != -1)
				o = o.substring(0, o.indexOf('?'));

			o = o.substring(0, o.lastIndexOf('/') + 1);

			t.settings.default_base_url = unescape(o);

			// Find script base URL
			function get(nl) {
				var i, n;

				for (i=0; i<nl.length; i++) {
					n = nl[i];

					cp.push(n);

					if (n.src && /mcfilemanager\.js/g.test(n.src))
						return n.src.substring(0, n.src.lastIndexOf('/'));
				}
			};

			o = d.documentElement;
			if (o && (o = get(o.getElementsByTagName('script'))))
				return t.baseURL = o;

			o = d.getElementsByTagName('script');
			if (o && (o = get(o)))
				return t.baseURL = o;

			o = d.getElementsByTagName('head')[0];
			if (o && (o = get(o.getElementsByTagName('script'))))
				return t.baseURL = o;
		},

		relaxDomain : function() {
			var t = this, p = /(http|https):\/\/([^\/:]+)\/?/.exec(t.baseURL);

			// Use tinymce relaxed domain
			if (window.tinymce && tinymce.relaxedDomain && tinymce.relaxedDomain != document.location.hostname) {
				t.relaxedDomain = tinymce.relaxedDomain;
				return;
			}

			// Relax domain
			if (p && p[2] != document.location.hostname)
				document.domain = t.relaxedDomain = p[2].replace(/.*\.(.+\..+)$/, '$1');
		},

		init : function(s) {
			this.extend(this.settings, s);
		},

		browse : function(s) {
			var t = this;

			s = s || {};

			if (s.fields) {
				s.oninsert = function(o) {
					t.each(s.fields.replace(/\s+/g, '').split(/,/), function(v) {
						var n;

						if (n = document.getElementById(v))
							t._setVal(n, o.focusedFile.url);
					});
				};
			}
			this.openWin({page : 'index.html'}, s);
		},

		edit : function(s) {
			this.openWin({page : 'edit.html', width : 800, height : 500}, s);
		},

		upload : function(s) {
			this.openWin({page : 'upload.html', width : 550, height : 350}, s);
		},

		createDoc : function(s) {
			this.openWin({page : 'createdoc.html', width : 450, height : 280}, s);
		},

		createDir : function(s) {
			this.openWin({page : 'createdir.html', width : 450, height : 280}, s);
		},

		createZip : function(s) {
			this.openWin({page : 'createzip.html', width : 450, height : 280}, s);
		},

		openWin : function(f, a) {
			var t = this, v, w;

			t.windowArgs = a = t.extend({}, t.settings, a);
			f = t.extend({
				x : -1,
				y : -1,
				width : 800,
				height : 500,
				inline : 1
			}, f);

			if (f.page)
				f.url = t.baseURL + '/../index.php?type=fm&page=' + f.page;

			if (a.session_id)
				f.url += '&sessionid=' + a.session_id;

			if (a.custom_data)
				f.url += '&custom_data=' + escape(a.custom_data);

			if (t.relaxedDomain)
				f.url += '&domain=' + escape(t.relaxedDomain);

			if (a.custom_query)
				f.url += a.custom_query;

			// Open in specified frame
			if (a.target_frame) {
				if (v = frames[a.target_frame])
					v.document.location = f.url;

				if (v = document.getElementById(a.target_frame))
					v.src = f.url;

				return;
			}

			// Use jQuery WindowManager
			if (window.jQuery && jQuery.WindowManager)
				return jQuery.WindowManager.open(f, a);

			// Use TinyMCE window API
			if (window.tinymce && tinyMCE.activeEditor)
				return tinyMCE.activeEditor.windowManager.open(f, a);

			if (f.x == -1)
				f.x = parseInt(screen.width / 2.0) - (f.width / 2.0);

			if (f.y == -1)
				f.y = parseInt(screen.height / 2.0) - (f.height / 2.0);

			// Use native dialogs
			w = window.open(f.url, 'mcFileManagerWin', 'left=' + f.x + 
				',top=' + f.y + ',width=' + f.width + ',height=' + 
				f.height + ',scrollbars=' + (f.scrollbars ? 'yes' : 'no') + 
				',resizable=' + (f.resizable ? 'yes' : 'no') + 
				',statusbar=' + (f.statusbar ? 'yes' : 'no')
			);

			try {
				w.focus();
			} catch (ex) {
				// Ignore
			}
		},

		each : function(o, f, s) {
			var n, l;

			if (o) {
				s = s || o;

				if (o.length !== undefined) {
					for (n = 0, l = o.length; n < l; n++)
						f.call(s, o[n], n, o);
				} else {
					for (n in o) {
						if (o.hasOwnProperty(n))
							f.call(s, o[n], n, o);
					}
				}
			}
		},

		extend : function() {
			var k, a = arguments, t = a[0], i, v;

			for (i = 1; i < a.length; i++) {
				if (v = a[i]) {
					for (k in v)
						t[k] = v[k];
				}
			}

			return t;
		},

		// Legacy functions
		open : function(fn, en, url, cb, s) {
			var t = this, el;

			s = s || {};

			// Use input value if it was found
			if (!s.url && document.forms[fn] && (el = document.forms[fn].elements[en.split(',')[0]]))
				s.url = el.value;

			if (!cb) {
				s.oninsert = function(o) {
					var e, i, v, f = o.focusedFile;

					v = en.replace(/\s+/g, '').split(',');

					for (i = 0; i < v.length; i++) {
						if (e = document.forms[fn][v[i]])
							t._setVal(e, f.url);
					}
				};
			} else {
				if (typeof(cb) == 'string')
					cb = window[cb];

				s.oninsert = function(o) {
					cb(o.focusedFile.url, o);
				};
			}

			t.browse(s);
		},

		filebrowserCallBack : function(fn, u, ty, w, ask) {
			var t = mcFileManager, i, hl, fo, s = {};

			// Is imagemanager included, ask it first
			if (window.mcImageManager && !ask) {
				hl = mcImageManager.settings.handle;

				hl = hl.split(',');
				for (i = 0; i < hl.length; i++) {
					if (ty == hl[i])
						fo = 1;
				}

				if (fo && mcImageManager.filebrowserCallBack(fn, u, ty, w, 1))
					return;
			}

			// Grab filemanager prefixed options
			t.each(tinyMCE.activeEditor ? tinyMCE.activeEditor.settings : tinyMCE.settings, function(v, k) {
				if (k.indexOf('filemanager_') === 0)
					s[k.substring(12)] = v;
			});

			t.browse(t.extend(s, {
				url : w.document.forms[0][fn].value,
				relative_urls : 0,
				oninsert : function(o) {
					var f, u, na;

					f = w.document.forms[0];
					u = o.focusedFile.url;
					inf = o.focusedFile.custom;

					// Let TinyMCE convert the URLs
					if (typeof(TinyMCE_convertURL) != "undefined")
						u = TinyMCE_convertURL(u, null, true);
					else if (tinyMCE.convertURL)
						u = tinyMCE.convertURL(u, null, true);
					else
						u = tinyMCE.activeEditor.convertURL(u, null, true);

					// Set alt and title info
					if (inf.custom && inf.custom.description) {
						na = ['alt', 'title', 'linktitle'];
						for (i = 0; i < na.length; i++) {
							if (f.elements[na[i]])
								f.elements[na[i]].value = inf.custom.description;
						}
					}

					t._setVal(f[fn], u);

					w = null; // IE leak
				}
			}));

			return true;
		},

		_setVal : function(n, v) {
			n.value = v;

			try {
				n.onchange();
			} catch (e) {
				// Skip it
			}
		}
	};

	mcFileManager.setup();
	mcFileManager.relaxDomain();
})();