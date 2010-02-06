/**
 * Copyright 2004-2009, Moxiecode Systems AB, All rights reserved.
 */

(function($) {
	window.BaseManager = {
		currentWin : $.WindowManager.find(window),
		path : '{default}',
		visualPath : '',
		files : [],
		selectedFiles : [],
		focusedFile : null,
		demoMode : false,
		disabled : {},
		specialFolders : [],

		getFile : function(id) {
			var o;

			$(this.files).each(function() {
				if (this.id == id)
					o = this;
			});

			return o;
		},

		setDisabled : function(v, st) {
			this.disabled[v] = st;

			if (st)
				$('#' + v).addClass('disabled').addClass('deactivated');
			else
				$('#' + v).removeClass('disabled').removeClass('deactivated');
		},

		isDisabled : function(v) {
			return this.disabled[v] ? this.disabled[v] : 0;
		},

		addSpecialFolder : function(o) {
			this.specialFolders.push(o);
		},

		isDemo : function() {
			if (this.demoMode) {
				$.WindowManager.info($.translate('{#error.demo}')); 
				return true;
			}
		}
	};
})(jQuery);
