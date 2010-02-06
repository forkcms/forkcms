/**
 * $Id: editor_plugin_src.js 42 2006-08-08 14:32:24Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
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
			if (window.tinymce && tinymce.relaxedDomain) {
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

			if (f.x == -1)
				f.x = parseInt(screen.width / 2.0) - (f.width / 2.0);

			if (f.y == -1)
				f.y = parseInt(screen.height / 2.0) - (f.height / 2.0);

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

			// Use TinyMCE window API
			if (window.tinymce && tinyMCE.activeEditor)
				return tinyMCE.activeEditor.windowManager.open(f, a);

			// Use jQuery WindowManager
			if (window.jQuery && jQuery.WindowManager)
				return jQuery.WindowManager.open(f, a);

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

	var mcFileManagerPlugin = {
		getInfo : function() {
			return {
				longname : 'MCFileManager PHP',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://tinymce.moxiecode.com/plugins_filemanager.php',
				version : "3.1.1.4"
			};
		},

		convertURL : function(u) {
			// 2.x
			if (window.TinyMCE_convertURL)
				return TinyMCE_convertURL(u, null, true);

			// 2.x
			if (tinyMCE.convertURL)
				return tinyMCE.convertURL(u, null, true);

			// 3.x
			return tinyMCE.activeEditor.convertURL(u, null, true);
		},

		replace : function(t, d, e) {
			var i, r;

			if (typeof(t) != 'string')
				return t(d, e);

			function get(d, s) {
				for (i=0, r=d, s=s.split('.'); i<s.length; i++)
					r = r[s[i]];

				return r;
			};

			// Replace variables
			t = '' + t.replace(/\{\$([^\}]+)\}/g, function(a, b) {
				var l = b.split('|'), v = get(d, l[0]);

				// Default encoding
				if (l.length == 1 && e && e.xmlEncode)
					v = e.xmlEncode(v);

				// Execute encoders
				for (i=1; i<l.length; i++)
					v = e[l[i]](v, d, b);

				return v;
			});

			// Execute functions
			t = t.replace(/\{\=([\w]+)([^\}]+)\}/g, function(a, b, c) {
				return get(e, b)(d, b, c);
			});

			return t;
		}
	};

	// Setup TinyMCE 3.x plugin
	if (window.tinymce) {
		tinymce.create('tinymce.plugins.FileManagerPlugin', {
			init : function(ed, url) {
				var t = this;

				t.editor = ed;
				t.url = url;
				mcFileManager.baseURL = url + '/js';

				ed.settings.file_browser_callback = mcFileManager.filebrowserCallBack;
				mcFileManager.settings.handle = ed.getParam('filemanager_handle', mcFileManager.settings.handle);

				ed.addCommand('mceInsertFile', function(u, v) {
					var s = {};

					// Grab filemanager prefixed options
					tinymce.each(tinyMCE.activeEditor.settings, function(v, k) {
						if (k.indexOf('filemanager_') === 0)
							s[k.substring(12)] = v;
					});

					mcFileManager.browse(tinymce.extend(s, {
						oninsert : function(o) {
							var u, bm;

							if (bm = ed.windowManager.bookmark)
								ed.selection.moveToBookmark(bm);

							if (!ed.selection.isCollapsed()) {
								ed.execCommand("createlink", false, "javascript:mce_temp_url();");

								tinymce.grep(ed.dom.select('a'), function(n) {
									if (n.href == 'javascript:mce_temp_url();') {
										u = mcFileManagerPlugin.convertURL(o.focusedFile.url);
										n.href = u;
										ed.dom.setAttrib(n, 'mce_href', u);
									}
								});
							} else {
								ed.execCommand('mceInsertContent', false, mcFileManagerPlugin.replace(
									ed.getParam('filemanager_insert_template', '<a href="{$url}">{$name}</a>'),
									o.focusedFile,
									{
										urlencode : function(v) {
											return escape(v);
										},

										xmlEncode : function(v) {
											return tinymce.DOM.encode(v);
										}
									}
								));
							}
						}
					}, v));
				});
			},

			getInfo : function() {
				return mcFileManagerPlugin.getInfo();
			},

			createControl: function(n, cm) {
				var t = this, c, ed = t.editor, v;

				switch (n) {
					case 'insertfile':
						v = ed.getParam('filemanager_insert_template');

						if (v instanceof Array) {
							c = cm.createMenuButton('insertfile', {
								title : 'filemanager_insertfile_desc',
								image : t.url + '/pages/fm/img/insertfile.gif',
								icons : false
							});

							c.onRenderMenu.add(function(c, m) {
								tinymce.each(v, function(v) {
									m.add({title : v.title, onclick : function() {
										ed.execCommand('mceInsertFile', false, v);
									}});
								});
							});
						} else {
							c = cm.createButton('insertfile', {
								title : 'filemanager_insertfile_desc',
								image : t.url + '/pages/fm/img/insertfile.gif',
								onclick : function() {
									ed.execCommand('mceInsertFile', false, {template : v});
								}
							});
						}

						return c;
				}

				return null;
			}
		});

		tinymce.PluginManager.add('filemanager', tinymce.plugins.FileManagerPlugin);
		tinymce.ScriptLoader.load((tinymce.PluginManager.urls['filemanager'] || tinymce.baseURL + '/plugins/filemanager') + '/language/index.php?type=fm&format=tinymce_3_x&group=tinymce&prefix=filemanager_');
	}

	// Setup TinyMCE 2.x plugin
	if (window.TinyMCE_Engine) {
		var TinyMCE_FileManagerPlugin = {
			setup : function() {
				var b = (window.realTinyMCE || tinyMCE).baseURL;

				mcFileManager.baseURL = b + '/plugins/filemanager/js';
				document.write('<script type="text/javascript" src="' + b + '/plugins/filemanager/language/index.php?type=fm&format=tinymce&group=tinymce&prefix=filemanager_"></script>');
			},

			initInstance : function(ed) {
				ed.settings.file_browser_callback = 'mcFileManager.filebrowserCallBack';
				mcFileManager.settings.handle = tinyMCE.getParam('filemanager_handle', mcFileManager.settings.handle);
			},

			getControlHTML : function(cn) {
				switch (cn) {
					case "insertfile":
						return tinyMCE.getButtonHTML(cn, 'lang_filemanager_insertfile_desc', '{$pluginurl}/pages/fm/img/insertfile.gif', 'mceInsertFile', false);
				}

				return "";
			},
	
			getInfo : function() {
				return mcFileManagerPlugin.getInfo();
			},

			execCommand : function(id, el, cmd, ui, v) {
				var ed = tinyMCE.getInstanceById(id);

				if (cmd == 'mceInsertFile') {
					mcFileManager.browse(tinyMCE.extend({
						path : tinyMCE.getParam("filemanager_path"),
						rootpath : tinyMCE.getParam("filemanager_rootpath"),
						remember_last_path : tinyMCE.getParam("filemanager_remember_last_path"),
						custom_data : tinyMCE.getParam("filemanager_custom_data"),
						insert_filter : tinyMCE.getParam("filemanager_insert_filter"),
						oninsert : function(o) {
							var u, nl, i;

							if (!ed.selection.isCollapsed()) {
								ed.execCommand("createlink", false, "javascript:mce_temp_url();");

								nl = tinyMCE.selectElements(ed.getBody(), 'A', function(n) {
									return tinyMCE.getAttrib(n, 'href') == "javascript:mce_temp_url();";
								});

								for (i = 0; i < nl.length; i++) {
									u = mcFileManagerPlugin.convertURL(o.focusedFile.url);
									nl[i].href = u;
									nl[i].setAttribute('mce_href', u);
								}
							} else {
								ed.execCommand('mceInsertContent', false, mcFileManagerPlugin.replace(
									tinyMCE.getParam('filemanager_insert_template', '<a href="{$url}">{$name}</a>'),
									o.focusedFile,
									{
										urlencode : function(v) {
											return escape(v);
										},

										xmlEncode : function(v) {
											return tinyMCE.xmlEncode(v);
										}
									}
								));
							}
						}
					}, v));

					return true;
				}

				return false;
			}
		};

		TinyMCE_FileManagerPlugin.setup();
		tinyMCE.addPlugin('filemanager', TinyMCE_FileManagerPlugin);
	}
})();
