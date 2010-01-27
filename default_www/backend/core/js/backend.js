if(!jsBackend) { var jsBackend = new Object(); }

jsBackend = {
	// datamembers
	debug: true,
	
	// init, something like a constructor
	init: function() {
		jsBackend.balloons.init();
		jsBackend.controls.init();
		//jsBackend.effects.init();
		jsBackend.forms.init();
		jsBackend.layout.init();
		jsBackend.tabs.init();
		//jsBackend.tableSequenceByDragAndDrop.init();
	},
	
	// end
	eof: true
}	

jsBackend.balloons = {
	// init, something like a constructor
	init: function() {
		$('.balloon:visible').each(function() {
			// search linked element
			var linkedElement = $('*[rel='+ $(this).attr('id') +']');
			
			// linked item found?
			if(linkedElement != null)
			{
				// position
				$(this).css('position', 'absolute')
					   .css('top', linkedElement.offset().top + linkedElement.height() + 10)
					   .css('left', linkedElement.offset().left - 30);
			}
		});
	
		$('.toggleBalloon').click(function() {
			// get linked balloon
			var rel = $(this).attr('rel');
			// rel available? 
			if(rel != '') {
				// hide if already visible
				if($('#'+ rel).is(':visible')) $('#'+ rel).fadeOut(500);
				// not visible
				else
				{
					// position
					$('#'+ rel).css('position', 'absolute')
							   .css('top', $(this).offset().top + $(this).height() + 10)
							   .css('left', $(this).offset().left - 30);
					// show
					$('#'+ rel).fadeIn(500);
				}
			}
		});
	},
	// end
	eof: true
}



jsBackend.controls = {
	// init, something like a constructor
	init: function() { 
		jsBackend.controls.bindWorkingLanguageSelection();
		jsBackend.controls.bindFullWidthSwitch();
		jsBackend.controls.bindToggleDiv();
		jsBackend.controls.bindConfirm();
		jsBackend.controls.bindMassCheckbox();
	},
	// bind confirm message
	bindConfirm: function() {
		// initialize
		$('.askConfirmation').each(function() {
			// get id
			var id = $(this).attr('rel');
			var url = $(this).attr('href');
			
			// initialize
			$('#'+ id).dialog({ autoOpen: false, draggable: false, resizable: false, modal: true, 
								buttons: { '{$lblOK|ucfirst}': function() {
																	// close dialog
																	$(this).dialog('close');
																	
																	// goto link
																	window.location = url;
																},
										   '{$lblCancel|ucfirst}': function() { $(this).dialog('close'); }
										 }
							 });

		});

		// bind clicks
		$('.askConfirmation').live('click', function(evt) {
			// prevent default
			evt.preventDefault();
			
			// get id
			var id = $(this).attr('rel');
			
			// bind
			$('#'+ id).dialog('open');
		});
	},
	// toggle between full width and sidebar-layout
	bindFullWidthSwitch: function() {
		$('#fullwidthSwitch').bind('click', function(evt) {
			// prevent default behaviour
			evt.preventDefault();
			
			// toggle
			$('#pagesTree, #contentList').toggle();
			
			// add class
			$(this).parent().addClass('collapsed')
		});
	},
	// jaja dikke lul
	bindMassCheckbox: function() {
		$('th .checkboxHolder input:checkbox').bind('change', function(evt) {
			// check or uncheck all the checkboxes in this datagrid
			$($(this).closest('table').find('td input:checkbox')).attr('checked', $(this).is(':checked'));
		});
	},
	// toggle a div
	bindToggleDiv: function() {
		$('.toggleDiv').live('click', function(evt) {
			// prevent default
			evt.preventDefault();
			
			// get id
			var id = $(this).attr('href');
			
			// show/hide
			$(id).toggle();
			
			// set selected class on parent
			if($(id).is(':visible')) $(this).parent().addClass('selected');
			else $(this).parent().removeClass('selected');
		});
	},
	// togle between the working languages
	bindWorkingLanguageSelection: function() {
		$('#workingLanguage').bind('change', function(evt) {
			// preventDefault
			evt.preventDefault();

			// break the url int parts
			var urlChunks = document.location.pathname.split('/');
			
			// get the querystring, we will append it later
			var queryString = document.location.search;
			
			// replace the third element with the new language
			urlChunks[2] = $(this).val();

			// rebuild the url and redirect
			document.location.href = urlChunks.join('/') + queryString;
		});
	},
	// end
	eof: true
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
	
	// if a var highlightId exists it will be highlighted
	bindHighlight: function() {
		if(typeof highlightId != 'undefined') {
			var selector = highlightId;
			// if the element is a table-row we should highlight all cells in that row
			if($(highlightId)[0].tagName.toLowerCase == 'tr') { selector += ' td'; } 
			$(selector).effect('highlight', null, 5000);
		}		
	},
		
	// end
	eof: true
}

jsBackend.forms = {
	// init, something like a constructor
	init: function() { 
		jsBackend.forms.datefields();
		jsBackend.forms.submitWithLinks();
		jsBackend.forms.tagBoxes();
	},
	
	// 
	datefields: function() {
		// the default, nothing special
		if($('.inputDatefieldNormal').length > 0) {
			$('.inputDatefieldNormal').each(function() {
				var data = $(this).attr('rel').split(':::');
				$(this).datepicker({
									dateFormat: data[0],
									dayNames: ['{$locDayLongMon}', '{$locDayLongTue}', '{$locDayLongWed}', '{$locDayLongThu}', '{$locDayLongFri}', '{$locDayLongSat}', '{$locDayLongSat}'],
									dayNamesMin: ['{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}', '{$locDayShortSat}'],
									dayNamesShort: ['{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}', '{$locDayShortSat}'],
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
									dayNames: ['{$locDayLongMon}', '{$locDayLongTue}', '{$locDayLongWed}', '{$locDayLongThu}', '{$locDayLongFri}', '{$locDayLongSat}', '{$locDayLongSat}'],
									dayNamesMin: ['{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}', '{$locDayShortSat}'],
									dayNamesShort: ['{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}', '{$locDayShortSat}'],
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
									dayNames: ['{$locDayLongMon}', '{$locDayLongTue}', '{$locDayLongWed}', '{$locDayLongThu}', '{$locDayLongFri}', '{$locDayLongSat}', '{$locDayLongSat}'],
									dayNamesMin: ['{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}', '{$locDayShortSat}'],
									dayNamesShort: ['{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}', '{$locDayShortSat}'],
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
									dayNames: ['{$locDayLongMon}', '{$locDayLongTue}', '{$locDayLongWed}', '{$locDayLongThu}', '{$locDayLongFri}', '{$locDayLongSat}', '{$locDayLongSat}'],
									dayNamesMin: ['{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}', '{$locDayShortSat}'],
									dayNamesShort: ['{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}', '{$locDayShortSat}'],
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
	
	submitWithLinks: function() {
		// the html for the button that will replace the input[submit]
		var replaceHTML = '<a class="{class}" href="#"><span><span><span>{label}</span></span></span></a>';
		
		// are there any forms that should be submitted with a link?
		if($('form.submitWithLink').length > 0) {
			$('form.submitWithLink').each(function() {
				// get id
				var formId = $(this).attr('id');
				
				// validate id
				if(formId != '') {
					// loop every button to be replaced
					$('form#'+ formId + '.submitWithLink input:submit').each(function() {
						$(this).after(replaceHTML.replace('{label}', $(this).val()).replace('{class}', 'submitButton button ' + $(this).attr('class')))
								.css({position:'absolute', top:'-2000px'})
								.attr('tabindex', -1); 
					});

					// add onclick event for button (button can't have the name submit)
					$('form#'+ formId + ' a.submitButton').bind('click', function(evt) {
						evt.preventDefault();
						$('form#'+ formId).submit();
					});
				}
			});
		}
	},
	
	tagBoxes: function() {
		if($('input.tagBox').length > 0) {
			$('input.tagBox').tagBox({ emptyMessage: '{$msgNoTags}', addLabel: '{$lblAdd|ucfirst}', removeLabel: '{$lblDeleteTag|ucfirst}', autoCompleteUrl: '/backend/ajax.php?module=tags&action=autocomplete&language={$LANGUAGE}' });
		}
	},
	
	// end
	eof: true
}

jsBackend.layout = {
	// init, something like a constructor
	init: function() {
		// hovers
		$('.contentTitle').hover(function() { $(this).addClass('hover'); }, function() { $(this).removeClass('hover'); });
		$('.datagrid td a').hover(function() { $(this).parent().addClass('hover'); }, function() { $(this).parent().removeClass('hover'); });
	},
	// end
	eof: true
}

jsBackend.tabs = {
	// init, something like a constructor
	init: function() {
		if($('.tabs').length > 0) {
			$('.tabs').tabs();
		}
		
		if($('.tabSelect').length > 0) {
			$('.tabSelect').live('click', function(evt) {
				// prevent default
				evt.preventDefault();
				
				console.log($(this));
				
				$('.tabs').tabs('select', $(this).attr('href'));
				
			});
		}
	},
	
	// end
	eof: true
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
	eof: true
}

$(document).ready(function() { jsBackend.init(); });