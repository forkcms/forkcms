(function($){
	window.CreateDocDialog = {
		currentWin : $.WindowManager.find(window),
		fields : [],

		init : function() {
			var t = this, args, cleanNames;

			args = $.extend({
				path : '{default}',
				visual_path : '/'
			}, t.currentWin.getArgs());

			$('#content').show();
			$('#createin').html(args.visual_path);

			RPC.exec('fm.getConfig', {path : args.path}, function(data) {
				var config = data.result, templates = [], tpl;

				cleanNames = config['filesystem.clean_names'] == "true";

				$(config['filesystem.file_templates'].split(/[,;]/)).each(function(i, v) {
					v = $.trim(v).split('=');
					templates.push({name : /([^\/]+)$/.exec(v[0])[0], value : v[1] || v[0]});
				});

				if (config['createdoc.fields']) {
					$(config['createdoc.fields'].split(/[,;]/)).each(function(i, v) {
						v = $.trim(v).split('=');
						t.fields.push({title : /([^\/]+)$/.exec(v[0])[0], name : v[1] || v[0]});
					});
				}

				tpl = $.templateFromScript('#template_template');
				$(templates).each(function(i, v) {
					$('#template').append(tpl, v);
				});

				if (templates.length > 1)
					$('#template').parents('tr').show();

				tpl = $.templateFromScript('#field_template');
				$(t.fields).each(function(i, v) {
					$('#docnamerow').after(tpl, v);
				});

				cleanNames = config['filesystem.clean_names'] == "true";

				$('form').submit(function() {
					var rpcArgs;

					rpcArgs = {
						frompath0 : $('#template').val(),
						topath0 : args.path,
						toname0 : cleanNames ? $.cleanName($('#docname').val()) : $('#docname').val()
					};

					rpcArgs.fields = {};
					$(t.fields).each(function(i, v) {
						rpcArgs.fields[v.name] = $('#' + v.name).val();
					});

					if (!t.isDemo()) {
						RPC.exec('fm.createDocs', rpcArgs, function(data) {
							var res = RPC.toArray(data.result), s = t.currentWin.getArgs();

							if (!RPC.handleError({message : '{#error.createdoc_failed}', visual_path : args.visual_path, response : data})) {
								// Insert file
								if (s.oncreate) {
									RPC.insertFile({
										relative_urls : s.relative_urls,
										document_base_url : s.document_base_url,
										default_base_url : s.default_base_url,
										no_host : s.remove_script_host || s.no_host,
										path : res[0].tofile,
										progress_message : $.translate("{#message.insert}"),
										insert_filter : s.insert_filter,
										oninsert : function(o) {
											s.oncreate(o);
											t.currentWin.close();
										}
									});
								}
							}
						});
					}

					return false;
				});

				$('#showpreview').click(function() {
					if ($('#template').val()) {
						$.WindowManager.open({
							url : '../../stream/index.php?cmd=fm.streamFile&path=' + escape($('#template').val()),
							width : 750,
							height : 450,
							title : $.translate('{#createdoc.preview_title}', 1, {file : /([^\/]+)$/.exec($('#template').val())[0]})
						}).maximize();
					}
				});

				$('#cancel').click(function() {t.currentWin.close();});
			});
		},

		isDemo : function() {
			if (this.currentWin.getArgs().is_demo) {
				$.WindowManager.info($.translate('{#error.demo}')); 
				return true;
			}
		}
	};

	$(function() {
		CreateDocDialog.init();
	});
})(jQuery);
