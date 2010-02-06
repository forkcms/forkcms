/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($){
	var w, ed, wm, args = {};

	window.focus();

//	try {
		w = opener || parent;

		// Check mcFileManager
		if (w.mcImageManager)
			args = w.mcImageManager.windowArgs;

		// Check TinyMCE
		if (w.tinyMCE && (ed = w.tinyMCE.activeEditor)) {
			if (ed && (wm = ed.windowManager)) {
				if (!args && wm.params)
					args = wm.params;

				if (wm.setTitle)
					wm.setTitle(window, document.title);
			}
		}
/*	} catch (ex) {
	}*/

	if (!$.CurrentWindowManager) {
		// Add default window and add some methods to it
		$.WindowManager.defaultWin = {
			getArgs : function() {
				return args;
			},

			close : function() {
				// Restore selection but only when the window is opened in an iframe
				if (ed && wm.bookmark && top != window)
					ed.selection.moveToBookmark(wm.bookmark);

				if (wm)
					wm.close(window);
				else
					window.close();
			}
		};
	}
})(jQuery);
