(function($){
	var man = window.FileManager || window.ImageManager;

	man.addSpecialFolder({title : '{#history.special_folder_title}', path : 'history:///', type : 'history'});

	$().bind('filelist:changed', function() {
		if (man.path.indexOf('history://') != -1) {
			$(man.tools).each(function(i, v) {
				man.setDisabled(v, 1);
			});

			$(['insert', 'download', 'view']).each(function(i, v) {
				man.setDisabled(v, 0);
			});
		}
	});
})(jQuery);
