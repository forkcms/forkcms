(function($){
	window.CreateZipDialog = {
		currentWin : $.WindowManager.find(window),

		init : function() {
			var t = this, args;

			args = $.extend({
				path : '{default}',
				visual_path : '/'
			}, t.currentWin.getArgs());

			$('#content').show();
			$('#createin').html(args.visual_path);

			RPC.exec('fm.getConfig', {path : args.path}, function(data) {
				var config = data.result, templates = [], tpl, cleanNames;

				if (!RPC.handleError({message : 'Get config error', visual_path : t.visualPath, response : data})) {
					cleanNames = config['filesystem.clean_names'] == "true";

					$('form').submit(function() {
						var rpcArgs = {};

						$(args.files).each(function(i, f) {
							rpcArgs['frompath' + i] = f;
						});

						rpcArgs.topath = args.path;
						rpcArgs.toname = cleanNames ? $.cleanName($('#zipname').val()) : $('#zipname').val();

						if (!t.isDemo()) {
							RPC.exec('fm.createZip', rpcArgs, function (data) {
								if (!RPC.handleError({message : '{#error.createzip_failed}', visual_path : args.visual_path, response : data})) {
									if (args.oncreate)
										args.oncreate();

									t.currentWin.close();
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
		CreateZipDialog.init();
	});
})(jQuery);
