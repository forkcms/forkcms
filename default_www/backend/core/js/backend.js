if(!jsBackend) { var jsBackend = new Object(); }

jsBackend = {
	// datamembers
	debug: true,
	
	// init, something like a constructor
	init: function() {
		jsBackend.controls.init();
		jsBackend.effects.init();
		jsBackend.forms.init();
		jsBackend.tabs.init();
		jsBackend.tableSequenceByDragAndDrop.init();
	},
	
	// end
	_eoo: true
}	

jsBackend.controls = {
	// init, something like a constructor
	init: function() { 
		jsBackend.controls.bindConfirmation();
		jsBackend.controls.bindOpenDialog();
	},
	
	// links with a class "askConfirmation" will be ask a confirmation when clicked
	bindConfirmation: function() {
		// @todo review me
		$('a.askConfirmation').bind('click', function(evt) {
			evt.preventDefault();
			var clickedElement = $(this);
			var messageElement = $($(this).find('.message')[0]);
			
			messageElement.dialog({
				// on close we should restore the html, because someone made a wierd discission
				close: function(event, ui) { clickedElement.html(clickedElement.html() + '<span class="message" style="display: none;">'+ messageElement.html() +'</span>'); },
				title: clickedElement.attr('title'),
				draggable: false,
				modal: true,
				resizable: false,
				buttons: {'{$lblCancel|ucfirst}': function() { $(this).dialog('close'); }, '{$lblOK|ucfirst}': function() { $(this).dialog('close'); window.location = clickedElement.attr('href'); }}
			});
		});
	},
	
	// links with a class "openDialog" will open the dialog referenced in the href-attribute
	bindOpenDialog: function() {
		// create dialogs
		$('.dialogMessage').dialog({ modal: true, autoOpen: false, draggable: false, resizable: false,
									 buttons: { '{$lblOK|ucfirst}': function() { $(this).dialog('close'); } }	
									});
		
		$('.openDialog').bind('click', function(evt) {
			evt.preventDefault();
			$($(this).attr('href')).dialog('open');
		});
		
	},
	
	
	// end
	_eoo: true
}

jsBackend.effects = {
	// init, something like a constructor
	init: function() { 
		jsBackend.effects.bindFadeOutAfterMouseMove();
		jsBackend.effects.bindHighlight();
	},
	
	// when the mouse is moved, all items with a class "fadeOutAfterMouseMove" will fade away
	bindFadeOutAfterMouseMove: function() {
		$(document.body).bind('mousemove', function(evt) { $('.fadeOutAfterMouseMove').fadeOut(2500); });		
	},
	
	// if a var hilightId exists it will be hilighted
	bindHighlight: function() {
		if(typeof hilightId != 'undefined') {
			var selector = hilightId;
			// if the element is a table-row we should hilight all cells in that row
			if($(hilightId)[0].tagName.toLowerCase == 'tr') { selector += ' td'; } 
			$(selector).effect('highlight', null, 5000);
		}		
	},
		
	// end
	_eoo: true
}

jsBackend.forms = {
	// init, something like a constructor
	init: function() { 
		jsBackend.forms.datefields(); 
	},
	
	// 
	datefields: function() {
		// the default, nothing special
		if($('.inputDatefieldNormal').length > 0) {
			$('.inputDatefieldNormal').each(function() {
				var data = $(this).attr('rel').split(':::');
				$(this).datepicker({
									dateFormat: data[0],
									dayNames: ['{$locDayLong1}', '{$locDayLong2}', '{$locDayLong3}', '{$locDayLong4}', '{$locDayLong5}', '{$locDayLong6}', '{$locDayLong7}'],
									dayNamesMin: ['{$locDayShort1}', '{$locDayShort2}', '{$locDayShort3}', '{$locDayShort4}', '{$locDayShort5}', '{$locDayShort6}', '{$locDayShort7}'],
									dayNamesShort: ['{$locDayShort1}', '{$locDayShort2}', '{$locDayShort3}', '{$locDayShort4}', '{$locDayShort5}', '{$locDayShort6}', '{$locDayShort7}'],
									firstDay: data[1],
									hideIfNoPrevNext: true,
									monthNames: ['{$locMonthLong1}', '{$locMonthLong2}', '{$locMonthLong3}', '{$locMonthLong4}', '{$locMonthLong5}', '{$locMonthLong6}', '{$locMonthLong7}', '{$locMonthLong8}', '{$locMonthLong9}', '{$locMonthLong10}', '{$locMonthLong11}', '{$locMonthLong12}'],
									monthNamesShort: ['{$locMonthShort1}', '{$locMonthShort2}', '{$locMonthShort3}', '{$locMonthShort4}', '{$locMonthShort5}', '{$locMonthShort6}', '{$locMonthShort7}', '{$locMonthShort8}', '{$locMonthShort9}', '{$locMonthShort10}', '{$locMonthShort11}', '{$locMonthShort12}'],
									nextText: '{$lblNext}',
									prevText: '{$lblPrevious}'
								});
			});
		}
		
		// datefields that have a certain startdate
		if($('.inputDatefieldFrom').length > 0) {
			$('.inputDatefieldFrom').each(function() {
				var data = $(this).attr('rel').split(':::');
				$(this).datepicker({
									dateFormat: data[0],
									dayNames: ['{$locDayLong1}', '{$locDayLong2}', '{$locDayLong3}', '{$locDayLong4}', '{$locDayLong5}', '{$locDayLong6}', '{$locDayLong7}'],
									dayNamesMin: ['{$locDayShort1}', '{$locDayShort2}', '{$locDayShort3}', '{$locDayShort4}', '{$locDayShort5}', '{$locDayShort6}', '{$locDayShort7}'],
									dayNamesShort: ['{$locDayShort1}', '{$locDayShort2}', '{$locDayShort3}', '{$locDayShort4}', '{$locDayShort5}', '{$locDayShort6}', '{$locDayShort7}'],
									firstDay: data[1],
									hideIfNoPrevNext: true,
									monthNames: ['{$locMonthLong1}', '{$locMonthLong2}', '{$locMonthLong3}', '{$locMonthLong4}', '{$locMonthLong5}', '{$locMonthLong6}', '{$locMonthLong7}', '{$locMonthLong8}', '{$locMonthLong9}', '{$locMonthLong10}', '{$locMonthLong11}', '{$locMonthLong12}'],
									monthNamesShort: ['{$locMonthShort1}', '{$locMonthShort2}', '{$locMonthShort3}', '{$locMonthShort4}', '{$locMonthShort5}', '{$locMonthShort6}', '{$locMonthShort7}', '{$locMonthShort8}', '{$locMonthShort9}', '{$locMonthShort10}', '{$locMonthShort11}', '{$locMonthShort12}'],
									nextText: '{$lblNext}',
									prevText: '{$lblPrevious}',
									minDate: new Date(data[2].split('-')[0], parseInt(data[2].split('-')[1]) - 1, data[2].split('-')[2])
								});
			});
		}

		// datefields that have a certain enddate
		if($('.inputDatefieldTill').length > 0) {
			$('.inputDatefieldTill').each(function() {
				var data = $(this).attr('rel').split(':::');
				$(this).datepicker({
									dateFormat: data[0],
									dayNames: ['{$locDayLong1}', '{$locDayLong2}', '{$locDayLong3}', '{$locDayLong4}', '{$locDayLong5}', '{$locDayLong6}', '{$locDayLong7}'],
									dayNamesMin: ['{$locDayShort1}', '{$locDayShort2}', '{$locDayShort3}', '{$locDayShort4}', '{$locDayShort5}', '{$locDayShort6}', '{$locDayShort7}'],
									dayNamesShort: ['{$locDayShort1}', '{$locDayShort2}', '{$locDayShort3}', '{$locDayShort4}', '{$locDayShort5}', '{$locDayShort6}', '{$locDayShort7}'],
									firstDay: data[1],
									hideIfNoPrevNext: true,
									monthNames: ['{$locMonthLong1}', '{$locMonthLong2}', '{$locMonthLong3}', '{$locMonthLong4}', '{$locMonthLong5}', '{$locMonthLong6}', '{$locMonthLong7}', '{$locMonthLong8}', '{$locMonthLong9}', '{$locMonthLong10}', '{$locMonthLong11}', '{$locMonthLong12}'],
									monthNamesShort: ['{$locMonthShort1}', '{$locMonthShort2}', '{$locMonthShort3}', '{$locMonthShort4}', '{$locMonthShort5}', '{$locMonthShort6}', '{$locMonthShort7}', '{$locMonthShort8}', '{$locMonthShort9}', '{$locMonthShort10}', '{$locMonthShort11}', '{$locMonthShort12}'],
									nextText: '{$lblNext}',
									prevText: '{$lblPrevious}',
									maxDate: new Date(data[2].split('-')[0], parseInt(data[2].split('-')[1]) -1, data[2].split('-')[2])
								});
			});
		}

		// datefields that have a certain range
		if($('.inputDatefieldRange').length > 0) {
			$('.inputDatefieldRange').each(function() {
				var data = $(this).attr('rel').split(':::');
				$(this).datepicker({
									dateFormat: data[0],
									dayNames: ['{$locDayLong1}', '{$locDayLong2}', '{$locDayLong3}', '{$locDayLong4}', '{$locDayLong5}', '{$locDayLong6}', '{$locDayLong7}'],
									dayNamesMin: ['{$locDayShort1}', '{$locDayShort2}', '{$locDayShort3}', '{$locDayShort4}', '{$locDayShort5}', '{$locDayShort6}', '{$locDayShort7}'],
									dayNamesShort: ['{$locDayShort1}', '{$locDayShort2}', '{$locDayShort3}', '{$locDayShort4}', '{$locDayShort5}', '{$locDayShort6}', '{$locDayShort7}'],
									firstDay: data[1],
									hideIfNoPrevNext: true,
									monthNames: ['{$locMonthLong1}', '{$locMonthLong2}', '{$locMonthLong3}', '{$locMonthLong4}', '{$locMonthLong5}', '{$locMonthLong6}', '{$locMonthLong7}', '{$locMonthLong8}', '{$locMonthLong9}', '{$locMonthLong10}', '{$locMonthLong11}', '{$locMonthLong12}'],
									monthNamesShort: ['{$locMonthShort1}', '{$locMonthShort2}', '{$locMonthShort3}', '{$locMonthShort4}', '{$locMonthShort5}', '{$locMonthShort6}', '{$locMonthShort7}', '{$locMonthShort8}', '{$locMonthShort9}', '{$locMonthShort10}', '{$locMonthShort11}', '{$locMonthShort12}'],
									nextText: '{$lblNext}',
									prevText: '{$lblPrevious}',
									minDate: new Date(data[2].split('-')[0], parseInt(data[2].split('-')[1]) - 1, data[2].split('-')[2]),
									maxDate: new Date(data[3].split('-')[0], parseInt(data[3].split('-')[1]) - 1, data[3].split('-')[2])
								});
			});
		}
	},
	
	// end
	_eoo: true
}

jsBackend.tabs = {
	// init, something like a constructor
	init: function() {
		if($('.tabs').length > 0) $('.tabs').tabs();
	},
	
	// end
	_eoo: true
}

jsBackend.tableSequenceByDragAndDrop = {
	// init, something like a constructor
	init: function() {
		if($('.sequenceByDragAndDrop tbody').length > 0) {
			$('.sequenceByDragAndDrop tbody').sortable({
				items: 'tr',
				handle: 'td.dragAndDropHandle',
				placeholder: 'dragAndDropPlaceholder',
				forcePlaceholderSize: true,
				stop: function(event, ui) {
					// split url to buil the ajax-url
					var chunks = document.location.pathname.split('/');
					// buil ajax-url
					var url = '/backend/ajax.php?module=' + chunks[3] + '&action=sequence&language=' + chunks[2];
					// init var
					var rowIds = $(this).sortable('toArray');
					var newIdSequence = new Array();
					// loop rowIds
					for(var i in rowIds) newIdSequence.push(rowIds[i].split('-')[1]);
					
					$.ajax({cache: false, type: 'POST', dataType: 'json', 
						url: url,
						data: 'new_id_sequence=' + newIdSequence.join(','),
						success: function(data, textStatus) { 
							// not a succes so revert the changes
							if(data.code != 200) { $(this).sortable('cancel'); }
						
							// alert the user
							if(data.code != 200 && jsBackend.debug) { alert(data.message); }
						},
						error: function(XMLHttpRequest, textStatus, errorThrown) {
							// revert changes
							$(this).sortable('cancel');

							// alert the user
							if(jsBackend.debug) alert(textStatus);
						}
					});
				}
			});
		}
	},

	// end
	_eoo: true
}

$(document).ready(function() { jsBackend.init(); });