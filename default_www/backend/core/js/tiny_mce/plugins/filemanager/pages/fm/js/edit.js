(function($){
	window.EditDialog = {
		currentWin : $.WindowManager.find(window),

		init : function() {
			var t = this, args;

			args = $.extend({
				path : '{default}',
				visual_path : '/'
			}, t.currentWin.getArgs());

			$('#filepath').html($.translate('{#edit.file_path}') + " " +  args.visual_path);

			$.WindowManager.showProgress({message : $.translate("{#edit.loading_wait}")});
			RPC.exec('fm.loadContent', {path : args.path}, function(data) {
				$.WindowManager.hideProgress();

				if (!RPC.handleError({message : '{#error.loadcontent_failed}', visual_path : args.visual_path, response : data}))
					$('#textcontent').val(data.result.content);
			});

			$('form').submit(function() {
				if (!t.isDemo()) {
					$.WindowManager.showProgress({message : $.translate("{#edit.saving_wait}")});
					RPC.exec('fm.saveContent', {path : args.path, content : $('#textcontent').val()}, function(data) {
						$.WindowManager.hideProgress();

						if (!RPC.handleError({message : '{#error.savecontent_failed}', visual_path : args.visual_path, response : data})) {
							// Insert file
							if (args.onsave) {
								RPC.insertFile({
									relative_urls : args.relative_urls,
									document_base_url : args.document_base_url,
									default_base_url : args.default_base_url,
									no_host : args.remove_script_host || args.no_host,
									path : args.path,
									progress_message : $.translate("{#message.insert}"),
									insert_filter : args.insert_filter,
									oninsert : function(o) {
										args.onsave(o);
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

			t.resizeView();

			$(window).bind('resize', function(e) {
				t.resizeView();
			}); 
		},

		resizeView : function() {
			$('#textcontent').css({'width' : $.winWidth() - 50, 'height' : $.winHeight() - 150});
		}, 

		isDemo : function() {
			if (this.currentWin.getArgs().is_demo) {
				$.WindowManager.info($.translate('{#error.demo}')); 
				return true;
			}
		}
	};

	$(function(e) {
		EditDialog.init();
	});
})(jQuery);
