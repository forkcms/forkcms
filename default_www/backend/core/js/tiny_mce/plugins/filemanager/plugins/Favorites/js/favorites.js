(function($){
	var man = window.FileManager || window.ImageManager, type = window.FileManager ? 'fm' : 'im';

	man.addSpecialFolder({title : '{#favorites.special_folder_title}', path : 'favorite:///', type : 'favorites'});

	// Add menu items to context menu
	$().bind('DropMenu:beforeshow', function(e, m) {
		if (man.path.indexOf('://') == -1) {
			m.addSeparator();
			m.add({title : $.translate('{#favorites.addfavorites}'), disabled : man.isDisabled('addfavorites') || !man.selectedFiles.length, onclick : addFavorites});
		}

		if (man.path.indexOf('favorite://') != -1) {
			m.addSeparator();
			m.add({title : $.translate('{#favorites.removefavorites}'), disabled : man.isDisabled('removefavorites') || !man.selectedFiles.length, onclick : removeFavorites});
		}
	});

	$().bind('filelist:changed', function() {
		if (man.path.indexOf('favorite://') != -1) {
			$(man.tools).each(function(i, v) {
				man.setDisabled(v, 1);
			});
		}
	});

	function addFavorites() {
		var args = {};

		$(man.selectedFiles).each(function(i, f) {
			args['path' + i] = f.path;
		});

		RPC.exec(type + '.addFavorites', args, function(data) {
			RPC.handleError({message : '{#error.addfavorites_failed}', visual_path : args.visual_path, response : data});
		});
	};

	function removeFavorites() {
		var args = {};

		$(man.selectedFiles).each(function(i, f) {
			args['path' + i] = f.path;
		});

		RPC.exec(type + '.removeFavorites', args, function(data) {
			if (!RPC.handleError({message : '{#error.removefavorites_failed}', visual_path : args.visual_path, response : data}))
				man.listFiles();
		});
	};
})(jQuery);
