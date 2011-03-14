(function($){
	window.CreateDirDialog = {
		currentWin : $.WindowManager.find(window),

		init : function() {
			var t = this, args, cleanNames;

			args = $.extend({
				path : '{default}',
				visual_path : '/'
			}, t.currentWin.getArgs());

			// Add templates
			RPC.exec('fm.getConfig', {path : args.path}, function(data) {
				var config = data.result, templates = [], tpl;

				if (!RPC.handleError({message : 'Get config error', visual_path : t.visualPath, response : data})) {
					cleanNames = config['filesystem.clean_names'] == "true"; 

					$(config['filesystem.directory_templates'].split(/,/)).each(function(i, v) {
						if (v) {
							v = $.trim(v).split('=');
							templates.push({name : /([^\/]+)$/.exec(v[0])[0], value : v[1] || v[0]});
						}
					});

					if (config['filesystem.force_directory_template'] == "true") {
						$('#template').html('');

						if (templates.length == 1)
							$('#templaterow').hide();
					}

					if (templates.length == 0)
						$('#templaterow').hide();

					tpl = $.templateFromScript('#template_template');
					$(templates).each(function(i, v) {
						$('#template').append(tpl, v);
					});

					$('#content').show();
					$('#createin').html(args.visual_path);

					$('form').submit(function() {
						var tpl = $('#template').val(), name = $('#dirname').val();

						if (cleanNames)
							name = $.cleanName(name);

						if (!t.isDemo()) {
							RPC.exec('fm.createDirs', {path : args.path, name0 : name, template0 : tpl}, function(data) {
								var res = RPC.toArray(data.result), s = t.currentWin.getArgs();

								if (!RPC.handleError({message : '{#error.createdir_failed}', visual_path : args.visual_path, response : data})) {
									// Insert file
									if (s.oncreate) {
										RPC.insertFile({
											relative_urls : s.relative_urls,
											document_base_url : s.document_base_url,
											default_base_url : s.default_base_url,
											no_host : s.remove_script_host || s.no_host,
											path : res[0].file,
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

					$('#cancel').click(function() {t.currentWin.close();});
				}
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
		CreateDirDialog.init();
	});
})(jQuery);
