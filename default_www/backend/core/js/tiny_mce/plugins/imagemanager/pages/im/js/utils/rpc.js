/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($){
	window.RPC = {
		pageBaseURL : document.location.href.replace(/[^\/]+$/, ''),

		init : function() {
			$().ajaxError(function(e, req, se) {
				if (req.status)
					$.WindowManager.status({title : "The server response wasn't JSON format", content : req.responseText}); 
			});
		},

		toArray : function(res) {
			var fl = [];

			if (res && res.data && res.columns) {
				$(res.data).each(function(i) {
					var o = {index : i};

					$(this).each(function(i, v) {
						o[res.columns[i]] = v;
					});

					fl.push(o);
				});
			}

			return fl;
		},

		exec : function(m, ar, cb) {
			// Make RPC call
			$.post(RPC.pageBaseURL + '../../rpc/index.php', {
				json_data : $.toJSON({
					"method" : m,
					"params" :[ar],
					"id" : "c0"
				})
				}, cb, "json"
			);
		},

		handleError : function(args) {
			var t = this, errors, res = args.response.result, err = args.response.error;

			if (err) {
				if (err.level == 'AUTH') {
					if (res.login_url.indexOf('return_url') != -1)
						document.location = res.login_url;
					else if (res.login_url.indexOf('://') == -1)
						document.location = "../../" + res.login_url + "?return_url=" + escape(document.location);
					else
						document.location = res.login_url + "?return_url=" + escape(document.location);

					return true;
				}

				$.WindowManager.info($.translate(err.errstr));
				return true;
			} else {
				errors = [];

				$(this.toArray(res)).each(function(i, r) {
					var root;

					if (r.status && r.status.toLowerCase() != 'ok') {
						root = args.visual_path || '/';
						root = root.replace(/^\/([^\/]+)\/.*$/, '$1');

						if (r.fromfile != undefined) {
							r.fromfile = r.fromfile.replace(/\{[0-9]+\}/, root);
							r.tofile = r.tofile.replace(/\{[0-9]+\}/, root);
							errors.push({title : r.fromfile + " -> " + r.tofile, content : $.translate(r.message)});
						}

						if (r.file != undefined) {
							r.file = r.file.replace(/\{[0-9]+\}/, root);
							r.file = r.file.replace(/\/+/, '/');
							errors.push({title : r.file, content : $.translate(r.message)});
						}
					}
				});

				if (errors.length) {
					$.WindowManager.status({title : $.translate(args.message), content : errors});
					return true;
				}
			}
		},

		insertFiles : function(s) {
			var t = this, args = {};

			$(s.paths).each(function(i, v) {
				args['path' + i] = v; 
			});

			if (s.progress_message)
				$.WindowManager.showProgress({message : s.progress_message});

			RPC.exec('im.insertFiles', args, function (data) {
				var o = {files : []};

				$.WindowManager.hideProgress();

				$(RPC.toArray(data.result)).each(function(i, v) {
					var u = v.url;

					if (s.relative_urls)
						u = $.parseURI(s.document_base_url || s.default_base_url).toRelative(u);
					else if (s.document_base_url || s.no_host)
						u = $.parseURI(s.document_base_url).toAbsolute($.parseURI(u).getURI(1), s.no_host);

					v = {
						name : v.name,
						path : v.path,
						url : u,
						size : v.size,
						type : v.type,
						created : v.created,
						modified : v.modified,
						attribs : v.attribs,
						custom : $.extend({}, v.custom)
					};

					o.files.push(v);
				});

				if (s.oninsert) {
					if (s.insert_filter) {
						$(o.files).each(function(i, f) {
							s.insert_filter(f);
						});
					}

					s.oninsert(o);
				}
			});
		},

		insertFile : function(s) {
			var ins = s.oninsert;

			s.paths = [s.path];
			s.oninsert = function(o) {
				o.file = o.files[0];
				ins.call(this, o);
			};

			this.insertFiles(s);
		}
	};

	RPC.init();
})(jQuery);
