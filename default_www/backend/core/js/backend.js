if(!jsBackend) { var jsBackend = new Object(); }

jsBackend = {
	// datamembers
	debug: true,
	
	// init, something like a constructor
	init: function() {
		jsBackend.controls.init();
		jsBackend.effects.init();
		jsBackend.tabs.init();
		jsBackend.tableSequenceByDragAndDrop.init();
	}	
}

jsBackend.controls = {
	// init, something like a constructor
	init: function() { 
		jsBackend.controls.bindConfirmation(); 
	},
	
	// links with a class "askConfirmation" will be ask a confirmation when clicked
	bindConfirmation: function() {
		$('.askConfirmation').bind('click', function(evt) { return confirm($(this).attr('rel')); });
	},
	
	// end
	_eoo: true
}

jsBackend.effects = {
	// init, something like a constructor
	init: function() { 
		jsBackend.effects.bindFadeOutAfterMouseMove();
		jsBackend.effects.bindHilight();
	},
	
	// when the mouse is moved, all items with a class "fadeOutAfterMouseMove" will fade away
	bindFadeOutAfterMouseMove: function() {
		$(document.body).bind('mousemove', function(evt) { $('.fadeOutAfterMouseMove').fadeOut(2500); });		
	},
	
	// if a var hilightId exists it will be hilighted
	bindHilight: function() {
		if(typeof hilightId != 'undefined') {
			var selector = hilightId;
			// if the element is a table-row we should hilight all cells in that row
			if($(hilightId)[0].tagName.toLowerCase == 'tr') { selector += ' td'; } 
			$(selector).highlightFade({color: '#FFFF88', speed: 2500});
		}		
	},
		
	// end
	_eoo: true
}

jsBackend.tabs = {
	// members
	currentTab: 'first',
		
	// init, something like a constructor
	init: function() { 
		if($('.tabs').length > 0) {
			jsBackend.tabs.setCurrentTab();
			jsBackend.tabs.bindNavigation();
			jsBackend.tabs.parse();
		}
	},
	
	// hijack the links in the navigation, when clicked the currenttab should be recalculated
	bindNavigation: function() {
		if($('.tabs .tabsNavigation a').length > 0) {
			$('.tabs .tabsNavigation a').bind('click', function(evt) {
				evt.preventDefault();
				var chunks = $(this).attr('href').split('#');
				jsBackend.tabs.currentTab = (chunks.length > 1) ? chunks[1] : 'first';
				jsBackend.tabs.parse();
			});
		}
	},
	
	// set correct classes for all tabs
	parse: function() {
		// hide all tabs and remove classes
		if($('.tabs .tabsContent .tabTab').length > 0) {
			$('.tabs .tabsContent .tabTab').each(function() {
				$(this).hide();
				$(this).removeClass('selected');
			});
		}
		
		// set selected state
		if($('.tabs .tabsNavigation a').length > 0) {
			var first = true;
			$('.tabs .tabsNavigation a').each(function() {
				$(this).parent().removeClass('selected');
			
				// first tab
				if(jsBackend.tabs.currentTab == 'first' && first) {
					$(this).parent().addClass('selected');
					$('.tabs .tabsContent #first').show();
					$('.tabs .tabsContent #first').addClass('selected');
				}
				
				// current tab, but not first
				if($(this).attr('href').indexOf('#' + jsBackend.tabs.currentTab) != -1) {
					$(this).parent().addClass('selected');
					$('.tabs .tabsContent #' + jsBackend.tabs.currentTab).show();
					$('.tabs .tabsContent #' + jsBackend.tabs.currentTab).addClass('selected');
				}
				
				first = false;
			});
		}
	},
	
	// set the current tab (read from url)
	setCurrentTab: function() {
		var chunks = document.location.href.split('#');
		jsBackend.tabs.currentTab = (chunks.length > 1) ? chunks[1] : 'first';
	},
	
	// end
	_eoo: true
}

jsBackend.tableSequenceByDragAndDrop = {
	// init, something like a constructor
	init: function() {
		if($('.sequenceByDragAndDrop').length > 0) {
			$('.sequenceByDragAndDrop').tableDnD({
				dragHandle: 'dragAndDropHandle',
				onDragClass: 'isMoving',
				onDrop: function(table, row) {
					jsBackend.tableSequenceByDragAndDrop.alterSequence(table, row);
				}
			});
		}
	},

	// alter the sequence, we send the ids in the new order
	alterSequence: function(table, row) {
		var chunks = document.location.pathname.split('/');
		var url = '/backend/ajax.php?module=' + chunks[3] + '&action=sequence&language=' + chunks[2];
		var newIdSequence = new Array()
		$(table.tBodies[0].rows).each(function() { newIdSequence.push($(this).attr('rel')); });
		$.ajax({cache: false, type: 'POST', dataType: 'json', 
			url: url,
			data: 'new_id_sequence=' + newIdSequence.join(','),
			success: function(data, textStatus) { 
				if(data.code != 200 && jsBackend.debug) alert(data.message);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				if(jsBackend.debug) alert(textStatus); 
			}
		});
	},
	
	// end
	_eoo: true
}

$(document).ready(function() { jsBackend.init(); });