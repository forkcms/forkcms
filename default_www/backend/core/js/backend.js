if(!jsBackend) { var jsBackend = new Object(); }

jsBackend = {
	// datamembers
	debug: true,
	
	// init, something like a constructor
	init: function() {
		jsBackend.balloons.init();
		jsBackend.controls.init();
		jsBackend.forms.init();
		jsBackend.layout.init();
		jsBackend.messages.init();
		jsBackend.tabs.init();
		jsBackend.tooltip.init();
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
	
		$('.toggleBalloon').click(jsBackend.balloons.click);
	},
	click: function(evt) {
		var clickedElement = $(this);
		
		// get linked balloon
		var rel = clickedElement.attr('rel');

		// rel available? 
		if(rel != '') {
			// hide if already visible
			if($('#'+ rel).is(':visible')) {
				// hide
				$('#'+ rel).fadeOut(500);
				
				// unbind
				$(window).unbind('resize');
			}
		
			// not visible
			else
			{
				// position
				jsBackend.balloons.position(clickedElement, $('#'+ rel));
				
				// show
				$('#'+ rel).fadeIn(500);
				
				// bind resize
				$(window).resize(function() { jsBackend.balloons.position(clickedElement, $('#'+ rel)) });
			}
		}
	},
	position: function(clickedElement, element) {
		// position
		element.css('position', 'absolute')
				   .css('top', clickedElement.offset().top + clickedElement.height() + 10)
				   .css('left', clickedElement.offset().left - 30);
	},
	// end
	eof: true
}



jsBackend.controls = {
	// init, something like a constructor
	init: function() { 
		jsBackend.controls.bindCheckboxTextfieldCombo();
		jsBackend.controls.bindConfirm();
		jsBackend.controls.bindFullWidthSwitch();
		jsBackend.controls.bindMassCheckbox();
		jsBackend.controls.bindPasswordStrengthMeter();
		jsBackend.controls.bindWorkingLanguageSelection();
		jsBackend.controls.bindTableCheckbox();
		jsBackend.controls.bindToggleDiv();
	},
	// bind a checkbox textfield combo
	bindCheckboxTextfieldCombo: function() {
		$('.checkboxTextFieldCombo').each(function() {
			// check if needed element exists
			if($(this).find('input:checkbox').length > 0 && $(this).find('input:text').length > 0) {
				var checkbox = $($(this).find('input:checkbox')[0]);
				var textField = $($(this).find('input:text')[0])

				checkbox.bind('change', function(evt) {
					var combo = $(this).parents().filter('.checkboxTextFieldCombo');
					var field = $(combo.find('input:text')[0]);
					
					if($(this).is(':checked')) {
						field.removeClass('disabled').attr('disabled', '');
						field.focus();
					}
					else field.addClass('disabled').attr('disabled', 'disabled');
				});
				
				if(checkbox.is(':checked')) textField.removeClass('disabled').attr('disabled', '');
				else textField.addClass('disabled').attr('disabled', 'disabled');
			}
		});
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
										 },
								open: function(evt) { 
											 // set focus on first button
											 if($(this).next().find('button').length > 0) { $(this).next().find('button')[0].focus(); } 
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
		$('#fullwidthSwitch a').toggle(function(evt) {
			// prevent default behaviour
			evt.preventDefault();
			
			// add class
			$(this).parent().addClass('collapsed')

			// toggle
			$('#subnavigation, #pagesTree').fadeOut(250);
			
			
		}, function(evt) {
			// Stuff to do every *even* time the element is clicked;
			evt.preventDefault();

			// remove class
			$(this).parent().removeClass('collapsed')
			
			// toggle
			$('#subnavigation, #pagesTree').fadeIn(500);

		});
		
		
	},
	// check all checkboxes with one checkbox in the tableheader
	bindMassCheckbox: function() {
		$('th .checkboxHolder input:checkbox').bind('change', function(evt) {
			// check or uncheck all the checkboxes in this datagrid
			$($(this).closest('table').find('td input:checkbox')).attr('checked', $(this).is(':checked'));
			// set selected class
			if($(this).is(':checked')) $($(this).parents().filter('table')[0]).find('tbody tr').addClass('selected');
			else $($(this).parents().filter('table')[0]).find('tbody tr').removeClass('selected');
		});
	},
	bindPasswordStrengthMeter: function() {
		if($('.passwordStrength').length > 0) {
			$('.passwordStrength').each(function() {
				// grab id
				var id = $(this).attr('rel');
				var wrapperId = $(this).attr('id');
				
				// hide all
				$('#'+ wrapperId +' p.strength').hide();
				
				// excecute function directly
				var classToShow = jsBackend.controls.checkPassword($('#'+ id).val());
				
				// show
				$('#'+ wrapperId +' p.'+ classToShow).show(); 
				
				// bind keypress
				$('#'+ id).bind('keyup', function() {
					// hide all
					$('#'+ wrapperId +' p.strength').hide();
					
					// excecute function directly
					var classToShow = jsBackend.controls.checkPassword($('#'+ id).val());
					
					// show
					$('#'+ wrapperId +' p.'+ classToShow).show(); 
				});
				
				
			});
		}
	},
	// check a string for passwordstrength
	checkPassword: function(string) {
		// init vars
		var score = 0;
		var uniqueChars = [];

		// less then 4 chars isn't a valid password
		if(string.length <= 4) return 'none';

		// loop chars and add unique chars
		for(var i in string) if(uniqueChars.indexOf(string.charAt(i)) == -1) uniqueChars.push(string.charAt(i));
		
		// less then 3 unique chars is just week
		if(uniqueChars.length < 3) return 'weak';
		
		// more then 6 chars is good
		if(string.length >= 6) score++;
		
		// more then 8 is beter
		if(string.length >= 8) score++;
		
		// upper and lowercase?
		if((string.match(/[a-z]/)) && string.match(/[A-Z]/)) score += 2;
		
		// number?
		if(string.match(/\d+/)) score++;
		
		// special char?
		if(string.match(/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/)) score++;

		// strong password
		if(score >= 4) return 'strong';
		
		// ok
		if(score >= 2) return 'ok';

		// fallback
		return 'weak';
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
	// bind checkboxes in a row
	bindTableCheckbox: function() {
		// set classes
		$('tr td input:checkbox:checked').each(function() { $($(this).parents().filter('tr')[0]).addClass('selected'); });
		// bind change-events
		$('tr td input:checkbox').live('change', function(evt) {
			if($(this).is(':checked')) $($(this).parents().filter('tr')[0]).addClass('selected');
			else $($(this).parents().filter('tr')[0]).removeClass('selected');
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
									prevText: '{$lblPrevious}',
									showAnim: 'slideDown'
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
									minDate: new Date(data[2].split('-')[0], parseInt(data[2].split('-')[1]) - 1, data[2].split('-')[2]),
									showAnim: 'slideDown'
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
									maxDate: new Date(data[2].split('-')[0], parseInt(data[2].split('-')[1]) -1, data[2].split('-')[2]),
									showAnim: 'slideDown'
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
									maxDate: new Date(data[3].split('-')[0], parseInt(data[3].split('-')[1]) - 1, data[3].split('-')[2]),
									showAnim: 'slideDown'
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
				var dontSubmit = false;

				// validate id
				if(formId != '') {
					// loop every button to be replaced
					$('form#'+ formId + '.submitWithLink input:submit').each(function() {
						$(this).after(replaceHTML.replace('{label}', $(this).val()).replace('{class}', 'submitButton button ' + $(this).attr('class')))
								.css({position:'absolute', top:'-9000px', left: '-9000px'})
								.attr('tabindex', -1); 
					});

					// add onclick event for button (button can't have the name submit)
					$('form#'+ formId + ' a.submitButton').bind('click', function(evt) {
						evt.preventDefault();
						$('form#'+ formId).submit();
					});

					// dont submit the form on certain elements
					$('form#'+ formId + ' .dontSubmit').bind('focus', function() { dontSubmit = true; })
					$('form#'+ formId + ' .dontSubmit').bind('blur', function() { dontSubmit = false; })
					
					// hijack the submit event
					$('form#'+ formId).submit(function(evt) { return !dontSubmit; });
				}
			});
		}
	},
	
	tagBoxes: function() {
		if($('#sidebar input.tagBox').length > 0) { $('#sidebar input.tagBox').tagBox({ emptyMessage: '{$msgNoTags}', addLabel: '{$lblAdd|ucfirst}', removeLabel: '{$lblDeleteTag|ucfirst}', autoCompleteUrl: '/backend/ajax.php?module=tags&action=autocomplete&language={$LANGUAGE}' }); }
		if($('#leftColumn input.tagBox').length > 0) { $('#leftColumn input.tagBox').tagBox({ emptyMessage: '{$msgNoTags}', addLabel: '{$lblAdd|ucfirst}', removeLabel: '{$lblDeleteTag|ucfirst}', autoCompleteUrl: '/backend/ajax.php?module=tags&action=autocomplete&language={$LANGUAGE}', showIconOnly: false }); }
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
		
		jsBackend.layout.showBrowserWarning();
	},
	// if the browser isn't supported show a warning
	showBrowserWarning: function() {
		var showWarning = false;
		
		// check firefox
		if(jQuery.browser.mozilla) {
			// get version
			var version = parseInt(jQuery.browser.version.substr(0,3).replace(/\./g, ''))

			// lower then 3?
			if(version < 19) showWarning = true;
		}
		
		// check opera
		if(jQuery.browser.opera) {
			// get version
			var version = parseInt(jQuery.browser.version.substr(0,1))

			// lower then 9?
			if(version < 9) showWarning = true;
		}
		
		// check safari, should be webkit when using 1.4
		if(jQuery.browser.safari) {
			// get version
			var version = parseInt(jQuery.browser.version.substr(0,3))

			// lower then 9?
			if(version < 400) showWarning = true;
		}

		// check IE
		if(jQuery.browser.msie) {
			// get version
			var version = parseInt(jQuery.browser.version.substr(0,1))
			
			// lower or equal then 6
			if(version <= 6) showWarning = true;
		}

		// show warning if needed
		if(showWarning) $('#showBrowserWarning').show();
	},
	// end
	eof: true
}

jsBackend.messages = {
	timers: [],
	
	// init, something like a constructor
	init: function() {
		// bind close button
		$('#messaging .formMessage .iconClose').live('click', function(evt) {
			evt.preventDefault();
			jsBackend.messages.hide($($(this).parents('.formMessage')));
		});
	},
	// hide a message
	hide: function(element) {
		// fade out
		element.fadeOut();
	},
	// add a new message into the que
	add: function(type, content) {
		var uniqueId = 'e'+ new Date().getTime().toString();
		var html = '<div id="'+ uniqueId +'" class="formMessage '+ type +'Message" style="display: none;">'+
					'	<p>'+ content +'</p>'+
					'	<div class="buttonHolderRight">'+
					'		<a class="button icon linkButton iconClose iconOnly" href="#"><span><span><span>X</span></span></span></a>'+
					'	</div>'+
					'</div>';
		
		// prepend
		$('#messaging').prepend(html);
		
		// show
		$('#'+ uniqueId).fadeIn();
		
		// timeout
		if(type == 'notice') { setTimeout('jsBackend.messages.hide($("#'+ uniqueId +'"));', 20000); }
		if(type == 'success') { setTimeout('jsBackend.messages.hide($("#'+ uniqueId +'"));', 5000); }
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
				$('.tabs').tabs('select', $(this).attr('href'));
			});
		}
	},
	
	// end
	eof: true
}

jsBackend.tooltip = {
	// init, something like a constructor
	init: function() {
		if($('.help').length > 0) { 
			$('.help').tooltip({ effect: 'fade' })
					  .dynamic(); 
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
							if(data.code != 200) { 
								// revert
								$(this).sortable('cancel');
								// show message
								jsBackend.messages.add('error', 'alter sequence failed.');
							}
						
							// alert the user
							if(data.code != 200 && jsBackend.debug) { alert(data.message); }
						},
						error: function(XMLHttpRequest, textStatus, errorThrown) {
							// revert
							$(this).sortable('cancel');
							// show message
							jsBackend.messages.add('error', 'alter sequence failed.');

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