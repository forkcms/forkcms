if(!jsBackend) { var jsBackend = new Object(); }

jsBackend.pages = {
	init: function() {
		jsBackend.pages.tree.init();
	},
	
	// end
	eoo: true
}

jsBackend.pages.tree = {
	init: function() {
		var options = { ui: { theme_name: 'fork' },
						rules: { multiple: false, multitree: 'all', drag_copy: false },
						lang: { loading: '{$lblLoading|ucfirst}' },
						callback: {
							beforemove: jsBackend.pages.tree.beforeMove,
							onselect: jsBackend.pages.tree.onSelect,
							onmove: jsBackend.pages.tree.onMove,
						},
						types: {
							'default': { renameable: false, deletable: false, creatable: false, icon: { image: '/backend/modules/pages/js/jstree/themes/fork/icons.gif' } },
							'page': { icon: { position: '0 -171px' } },
							'folder': { icon: { position: false } },
							'hidden': { icon: { position: false } },
							'home': { draggable: false, icon: { position: '0 -189px' } },
							'pages': { icon: { position: false } },
							'error': { draggable: false, max_children: 0, icon: { position: '0 -227px' } },
							'sitemap': { max_children: 0, icon: { position: '0 -246px' } }
						},
						plugins: { 
							cookie: { prefix: 'jstree_', types: { selected: false } }
						}
					  };

		// create tree
		$('#tree div').tree(options);
	},
	
	// before an item will be moved we have to do some checks
	beforeMove: function(node, refNode, type, tree) {
		// get pageID that has to be moved
		var currentPageID = $(node).attr('id').replace('page-', '');
		
		// init var
		var result = false;
		
		// make the call @todo	Tijs, check: http://docs.jquery.com/Ajax/jQuery.ajaxSetup!
		$.ajax({ type: 'POST', dataType: 'json', 
				 async: false, // important that this isn't asynchronous
				 url: '/backend/ajax.php?module=pages&action=get_info&language={$LANGUAGE}',
				 data: 'id=' + currentPageID,
				 error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(jsBackend.debug) alert(textStatus);
					result = false;
				 },
				 success: function(json, textStatus) {
					 if(json.code != 200) {
						 if(jsBackend.debug) alert(textStatus);
						 result = false;
					 } else {
						 if(json.data.allow_move == 'Y') result = true;
					 }
				 },
		});

		// return
		return result;
	},
	// when an item is selected
	onSelect: function(node, tree) {
		// get current and new URL
		var currentPageURL = window.location.pathname + window.location.search; 
		var newPageURL = $(node).find('a').attr('href');
		
		// only redirect if destination isn't the current one.
		if(typeof newPageURL != 'undefined' && newPageURL != currentPageURL) window.location = newPageURL;
	},
	onMove: function(node, refNode, type, tree, rollback) {
		// get pageID that has to be moved
		var currentPageID = $(node).attr('id').replace('page-', '');
		
		// get pageID wheron the page has been dropped
		var droppedOnPageID = $(refNode).attr('id').replace('page-', '');

		// make the call @todo	Tijs, check: http://docs.jquery.com/Ajax/jQuery.ajaxSetup!
		$.ajax({ type: 'POST', dataType: 'json', 
				 url: '/backend/ajax.php?module=pages&action=move&language={$LANGUAGE}',
				 data: 'id=' + currentPageID + '&dropped_on='+ droppedOnPageID +'&type='+ type,
				 error: function(XMLHttpRequest, textStatus, errorThrown) {
					if(jsBackend.debug) alert(textStatus);
				 },
				 success: function(json, textStatus) {
					 if(json.code != 200) {
						 if(jsBackend.debug) alert(textStatus);
						 // show error
						 // @todo	show errormessage
						 // rollback
						 $.tree.rollback(rollback);
					 } else {
					 // @todo	show succesmessage
					 }
				 },
		});
	},
	
	// end
	eoo: true
}


$(document).ready(function() { jsBackend.pages.init(); });