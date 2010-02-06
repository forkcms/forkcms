/**
 * $Id: editor_plugin_src.js 42 2006-08-08 14:32:24Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2007, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	window.mcImageManager = {
		settings : {
			document_base_url : '',
			relative_urls : false,
			remove_script_host : false,
			use_url_path : true,
			remember_last_path : 'auto',
			target_elements : '',
			target_form : '',
			handle : 'image,media'
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

					if (n.src && /mcimagemanager\.js/g.test(n.src))
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

			this.openWin({page : 'index.html', scrollbars : 'yes'}, s);
		},

		edit : function(s) {
			this.openWin({page : 'edit.html', width : 800, height : 500}, s);
		},

		upload : function(s) {
			this.openWin({page : 'upload.html', width : 550, height : 350}, s);
		},

		view : function(s) {
			this.openWin({page : 'view.html', width : 800, height : 500}, s);
		},

		createDir : function(s) {
			this.openWin({page : 'createdir.html', width : 450, height : 280}, s);
		},

		openWin : function(f, a) {
			var t = this, w, v;

			t.windowArgs = a = t.extend({}, t.settings, a);
			f = t.extend({
				x : -1,
				y : -1,
				width : 810,
				height : 500,
				inline : 1
			}, f);

			if (f.x == -1)
				f.x = parseInt(screen.width / 2.0) - (f.width / 2.0);

			if (f.y == -1)
				f.y = parseInt(screen.height / 2.0) - (f.height / 2.0);

			if (f.page)
				f.url = t.baseURL + '/../index.php?type=im&page=' + f.page;

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
			w = window.open(f.url, 'mcImageManagerWin', 'left=' + f.x + 
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
			var t = mcImageManager, i, hl, fo, s = {};

			// Is filemanager included, ask it first
			if (window.mcFileManager && !ask) {
				hl = mcFileManager.settings.handle;

				hl = hl.split(',');
				for (i = 0; i < hl.length; i++) {
					if (ty == hl[i])
						fo = 1;
				}

				if (fo && mcFileManager.filebrowserCallBack(fn, u, ty, w, 1))
					return;
			}

			// Grab imagemanager prefixed options
			t.each(tinyMCE.activeEditor ? tinyMCE.activeEditor.settings : tinyMCE.settings, function(v, k) {
				if (k.indexOf('imagemanager_') === 0)
					s[k.substring(13)] = v;
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

	mcImageManager.setup();
	mcImageManager.relaxDomain();

	var mcImageManagerPlugin = {
		getInfo : function() {
			return {
				longname : 'MCImageManager PHP',
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
		tinymce.create('tinymce.plugins.ImageManagerPlugin', {
			init : function(ed, url) {
				var t = this;

				t.editor = ed;
				t.url = url;
				mcImageManager.baseURL = url + '/js';

				ed.settings.file_browser_callback = mcImageManager.filebrowserCallBack;
				mcImageManager.settings.handle = ed.getParam('imagemanager_handle', mcImageManager.settings.handle);

				ed.onInit.add(function() {
					if (ed && ed.plugins.contextmenu && ed.getParam('imagemanager_contextmenu', true)) {
						ed.plugins.contextmenu.onContextMenu.add(function(th, m, e) {
							var el = ed.selection.getNode();

							if (el && el.nodeName == 'IMG') {
								// Imagemanager
								m.addSeparator();
								sm = m.addMenu({title : 'ImageManager'});

								sm.add({
									title : 'imagemanager_replaceimage_desc',
									icon_src : url + '/pages/im/img/insertimage.gif',
									onclick : function() {
										mcImageManager.browse({
											path : ed.getParam("imagemanager_path"),
											rootpath : ed.getParam("imagemanager_rootpath"),
											remember_last_path : ed.getParam("imagemanager_remember_last_path"),
											custom_data : ed.getParam("imagemanager_custom_data"),
											insert_filter : ed.getParam("imagemanager_insert_filter"),
											oninsert : function(o) {
												ed.dom.setAttribs(el, {width : '', height : '', style : {width : '', height : ''}});
												ed.dom.setAttrib(el, 'src', o.focusedFile.url);
											}
										});
									}
								});

								sm.add({
									title : 'imagemanager_editimage_desc',
									icon_src : url + '/pages/im/img/editimage.gif',
									onclick : function() {
										mcImageManager.edit({
											insert_filter : ed.getParam("imagemanager_insert_filter"),
											url : ed.documentBaseURI.toAbsolute(ed.dom.getAttrib(el, 'src', ed.dom.getAttrib(el, 'src'))),
											onsave : function(o) {
												ed.dom.setAttribs(el, {width : '', height : '', style : {width : '', height : ''}});
												ed.dom.setAttrib(el, 'src', o.file.url);
											}
										});
									}
								});
							}
						});
					}
				});

				ed.addCommand('mceInsertImage', function(u, v) {
					var s = {};

					// Grab imagemanager prefixed options
					tinymce.each(tinyMCE.activeEditor.settings, function(v, k) {
						if (k.indexOf('imagemanager_') === 0)
							s[k.substring(13)] = v;
					});

					mcImageManager.browse(tinymce.extend(s, {
						oninsert : function(o) {
							var ci = o.focusedFile.custom;

							if (!ci.thumbnail_url) {
								ci.thumbnail_url = url;
								ci.twidth = ci.width;
								ci.theight = ci.height;
							}

							ed.execCommand('mceInsertContent', false, mcImageManagerPlugin.replace(
								ed.getParam('imagemanager_insert_template', '<img src="{$url}" width="{$custom.width}" height="{$custom.height}" />'),
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
					}, v));
				});
			},

			getInfo : function() {
				return mcImageManagerPlugin.getInfo();
			},

			createControl: function(n, cm) {
				var t = this, c, ed = t.editor, v;

				switch (n) {
					case 'insertimage':
						v = ed.getParam('imagemanager_insert_template');

						if (v instanceof Array) {
							c = cm.createMenuButton('insertimage', {
								title : 'imagemanager_insertimage_desc',
								image : t.url + '/pages/im/img/insertimage.gif',
								icons : false
							});

							c.onRenderMenu.add(function(c, m) {
								tinymce.each(v, function(v) {
									m.add({title : v.title, onclick : function() {
										ed.execCommand('mceInsertImage', false, v);
									}});
								});
							});
						} else {
							c = cm.createButton('insertimage', {
								title : 'imagemanager_insertimage_desc',
								image : t.url + '/pages/im/img/insertimage.gif',
								onclick : function() {
									ed.execCommand('mceInsertImage', false, {template : v});
								}
							});
						}

						return c;
				}

				return null;
			}
		});

		tinymce.PluginManager.add('imagemanager', tinymce.plugins.ImageManagerPlugin);
		tinymce.ScriptLoader.load((tinymce.PluginManager.urls['imagemanager'] || tinymce.baseURL + '/plugins/imagemanager')+ '/language/index.php?type=im&format=tinymce_3_x&group=tinymce&prefix=imagemanager_');
	}

	// Setup TinyMCE 2.x plugin
	if (window.TinyMCE_Engine) {
		var TinyMCE_ImageManagerPlugin = {
			setup : function() {
				var b = (window.realTinyMCE || tinyMCE).baseURL;

				mcImageManager.baseURL = b + '/plugins/imagemanager/js';
				document.write('<script type="text/javascript" src="' + b + '/plugins/imagemanager/language/index.php?type=im&format=tinymce&group=tinymce&prefix=imagemanager_"></script>');
			},

			initInstance : function(ed) {
				ed.settings.file_browser_callback = 'mcImageManager.filebrowserCallBack';
				mcImageManager.settings.handle = tinyMCE.getParam('imagemanager_handle', mcImageManager.settings.handle);
			},

			getControlHTML : function(cn) {
				switch (cn) {
					case "insertimage":
						return tinyMCE.getButtonHTML(cn, 'lang_imagemanager_insertimage_desc', '{$pluginurl}/pages/im/img/insertimage.gif', 'mceInsertImage', false);
				}

				return "";
			},
	
			getInfo : function() {
				return mcImageManagerPlugin.getInfo();
			},

			execCommand : function(id, el, cmd, ui, v) {
				var ed = tinyMCE.getInstanceById(id);

				if (cmd == 'mceInsertImage') {
					mcImageManager.browse(tinyMCE.extend({
						path : tinyMCE.getParam("imagemanager_path"),
						rootpath : tinyMCE.getParam("imagemanager_rootpath"),
						remember_last_path : tinyMCE.getParam("imagemanager_remember_last_path"),
						custom_data : tinyMCE.getParam("imagemanager_custom_data"),
						insert_filter : tinyMCE.getParam("imagemanager_insert_filter"),
						oninsert : function(o) {
							var ci = o.focusedFile.custom;

							if (!ci.thumbnail_url) {
								ci.thumbnail_url = url;
								ci.twidth = ci.width;
								ci.theight = ci.height;
							}

							ed.execCommand('mceInsertContent', false, mcImageManagerPlugin.replace(
								tinyMCE.getParam('imagemanager_insert_template', '<a href="{$url}" rel="lightbox"><img src="{$custom.thumbnail_url}" width="{$custom.twidth}" height="{$custom.theight}" /></a>'),
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
					}, v));

					return true;
				}

				return false;
			}
		};

		TinyMCE_ImageManagerPlugin.setup();
		tinyMCE.addPlugin('imagemanager', TinyMCE_ImageManagerPlugin);
	}
})();
