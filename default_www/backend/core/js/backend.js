if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Backend related objects
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Dieter Vanden Eynde <dieter@netlash.com>
 */
jsBackend =
{
	// datamembers
	debug: {option:SPOON_DEBUG}true{/option:SPOON_DEBUG}{option:!SPOON_DEBUG}false{/option:!SPOON_DEBUG},
	current:
	{
		module: null,
		action: null,
		language: null
	},


	// init, something like a constructor
	init: function()
	{
		// get url and split into chunks
		var chunks = document.location.pathname.split('/');

		// set some properties
		jsBackend.current.module = chunks[3];
		jsBackend.current.action = chunks[4];
		jsBackend.current.language = chunks[2];

		// init stuff
		jsBackend.initAjax();
		jsBackend.balloons.init();
		jsBackend.controls.init();
		jsBackend.effects.init();
		jsBackend.tabs.init();
		jsBackend.forms.init();
		jsBackend.layout.init();
		jsBackend.messages.init();
		jsBackend.tooltip.init();
		jsBackend.tableSequenceByDragAndDrop.init();
		jsBackend.tinyMCE.init();

		// IE fixes
		jsBackend.selectors.init();
		jsBackend.focusfix.init();

		// do not move, should be run as the last item.
		jsBackend.forms.unloadWarning();
	},


	// init ajax
	initAjax: function()
	{
		// set defaults for AJAX
		$.ajaxSetup(
		{
			cache: false,
			type: 'POST',
			dataType: 'json',
			timeout: 10000
		});

		// global error handler
		$(document).ajaxError(function(event, XMLHttpRequest, ajaxOptions)
		{
			// 403 means we aren't authenticated anymore, so reload the page
			if(XMLHttpRequest.status == 403) window.location.reload();

			// check if a custom errorhandler is used
			if(typeof ajaxOptions.error == 'undefined')
			{
				// init var
				var textStatus = '{$errSomethingWentWrong}';

				// get real message
				if(typeof XMLHttpRequest.responseText != 'undefined') textStatus = $.parseJSON(XMLHttpRequest.responseText).message;

				// show message
				jsBackend.messages.add('error', textStatus);
			}
		});

		// spinner stuff
		$(document).ajaxStart(function() { $('#ajaxSpinner').show(); });
		$(document).ajaxStop(function() { $('#ajaxSpinner').hide(); });
	},


	// end
	eoo: true
}


/**
 * Handle form messages (action feedback: success, error, ...)
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.balloons =
{
	// init, something like a constructor
	init: function()
	{
		$('.balloon:visible').each(function()
		{
			// search linked element
			var linkedElement = $('*[data-message-id='+ $(this).attr('id') +']');

			// linked item found?
			if(linkedElement != null)
			{
				// position
				$(this).css('position', 'absolute').css('top', linkedElement.offset().top + linkedElement.height() + 10).css('left', linkedElement.offset().left - 30);
			}
		});

		$('.toggleBalloon').click(jsBackend.balloons.click);
	},


	// handle the click event (make it appear/disappear)
	click: function(evt)
	{
		var clickedElement = $(this);

		// get linked balloon
		var id = clickedElement.data('messageId');
		
		// rel available?
		if(id != '')
		{
			// hide if already visible
			if($('#'+ id).is(':visible'))
			{
				// hide
				$('#'+ id).fadeOut(500);

				// unbind
				$(window).unbind('resize');
			}

			// not visible
			else
			{
				// position
				jsBackend.balloons.position(clickedElement, $('#'+ id));

				// show
				$('#'+ id).fadeIn(500);

				// set focus on first visible field
				$('#'+ id +' form input:visible:first').focus();

				// bind resize
				$(window).resize(function() { jsBackend.balloons.position(clickedElement, $('#'+ id)); });
			}
		}
	},


	// position the balloon
	position: function(clickedElement, element)
	{
		// position
		element.css('position', 'absolute').css('top', clickedElement.offset().top + clickedElement.height() + 10).css('left', clickedElement.offset().left - 30);
	},


	// end
	eoo: true
}


/**
 * Handle form functionality
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.controls =
{
	// init, something like a constructor
	init: function()
	{
		jsBackend.controls.bindCheckboxDropdownCombo();
		jsBackend.controls.bindCheckboxTextfieldCombo();
		jsBackend.controls.bindRadioButtonFieldCombo();
		jsBackend.controls.bindConfirm();
		jsBackend.controls.bindFakeDropdown();
		jsBackend.controls.bindFullWidthSwitch();
		jsBackend.controls.bindMassCheckbox();
		jsBackend.controls.bindMassAction();
		jsBackend.controls.bindPasswordGenerator();
		jsBackend.controls.bindPasswordStrengthMeter();
		jsBackend.controls.bindWorkingLanguageSelection();
		jsBackend.controls.bindTableCheckbox();
		jsBackend.controls.bindTargetBlank();
		jsBackend.controls.bindToggleDiv();
	},


	// bind a checkbox textfield combo
	bindCheckboxDropdownCombo: function()
	{
		$('.checkboxDropdownCombo').each(function()
		{
			// check if needed element exists
			if($(this).find('input:checkbox').length > 0 && $(this).find('select').length > 0)
			{
				var checkbox = $(this).find('input:checkbox').eq(0);
				var dropdown = $(this).find('select').eq(0);

				checkbox.bind('change', function(evt)
				{
					var combo = $(this).parents().filter('.checkboxDropdownCombo');
					var field = $(combo.find('select')[0]);

					if($(this).is(':checked'))
					{
						field.removeClass('disabled').prop('disabled', false);
						field.focus();
					}

					else field.addClass('disabled').prop('disabled', true);
				});

				if(checkbox.is(':checked')) dropdown.removeClass('disabled').prop('disabled', false);
				else dropdown.addClass('disabled').prop('disabled', true);
			}
		});
	},


	// bind a checkbox textfield combo
	bindCheckboxTextfieldCombo: function()
	{
		$('.checkboxTextFieldCombo').each(function()
		{
			// check if needed element exists
			if($(this).find('input:checkbox').length > 0 && $(this).find('input:text').length > 0)
			{
				var checkbox = $(this).find('input:checkbox').eq(0);
				var textField = $(this).find('input:text').eq(0);

				checkbox.bind('change', function(evt)
				{
					var combo = $(this).parents().filter('.checkboxTextFieldCombo');
					var field = $(combo.find('input:text')[0]);

					if($(this).is(':checked'))
					{
						field.removeClass('disabled').prop('disabled', false);
						field.focus();
					}

					else field.addClass('disabled').prop('disabled', true);
				});

				if(checkbox.is(':checked')) textField.removeClass('disabled').prop('disabled', false);
				else textField.addClass('disabled').prop('disabled', true);
			}
		});
	},


	// bind a radiobutton field combo
	bindRadioButtonFieldCombo: function()
	{
		$('.radiobuttonFieldCombo').each(function()
		{
			// check if needed element exists
			if($(this).find('input:radio').length > 0 && $(this).find('input, select, textarea').length > 0)
			{
				var radiobutton = $(this).find('input:radio');

				radiobutton.bind('change', function(evt)
				{
					// redefine
					$this = $(this);
					
					// disable all
					$this.parents('.radiobuttonFieldCombo:first').find('input:not([name='+ radiobutton.attr('name') +']), select, textarea').addClass('disabled').prop('disabled', true);
					
					// get fields
					var fields = $this.parents('li').find('input:not([name='+ radiobutton.attr('name') +']), select, textarea')

					// enable
					fields.removeClass('disabled').prop('disabled', false);
					
					// set focus
					$(fields[0]).focus();
				});
				
				// change?
				$(radiobutton[0]).change();
			}
		});
	},
	
	// bind confirm message
	bindConfirm: function()
	{
		// initialize
		$('.askConfirmation').each(function()
		{
			// get id
			var id = $(this).data('messageId');
			var url = $(this).attr('href');

			if(id != '' && url != '')
			{
				// initialize
				$('#'+ id).dialog(
				{
					autoOpen: false,
					draggable: false,
					resizable: false,
					modal: true,
					buttons:
					{
						'{$lblOK|ucfirst}': function()
						{
							// unbind the beforeunload event
							$(window).unbind('beforeunload');

							// close dialog
							$(this).dialog('close');

							// goto link
							window.location = url;
						},
						'{$lblCancel|ucfirst}': function()
						{
								$(this).dialog('close');
						}
					},
					open: function(evt)
					{
						// set focus on first button
						if($(this).next().find('button').length > 0) $(this).next().find('button')[0].focus();
					}
				});
			}
		});

		// bind clicks
		$('.askConfirmation').live('click', function(evt)
		{
			// prevent default
			evt.preventDefault();

			// get id
			var id = $(this).data('messageId');

			// bind
			if(id != '')
			{
				// set target
				$('#'+ id).data('messageId', $(this).attr('href'));

				// open dialog
				$('#'+ id).dialog('open');
			}
		});
	},


	// let the fake dropdown behave nicely, like a real dropdown
	bindFakeDropdown: function()
	{
		$('.fakeDropdown').bind('click', function(evt)
		{
			// prevent default behaviour
			evt.preventDefault();

			// stop it
			evt.stopPropagation();

			// get id
			var id = $(this).attr('href');
			
			// IE8 prepends full current url before links to #
			id = id.substring(id.indexOf('#'));

			if($(id).is(':visible'))
			{
				// remove events
				$('body').unbind('click');
				$('body').unbind('keyup');

				// remove class
				$(this).parent().removeClass('selected');

				// hide
				$(id).hide('blind', {}, 'fast');
			}
			else
			{
				// bind escape
				$('body').bind('keyup', function(evt)
				{
					if(evt.keyCode == 27)
					{
						// unbind event
						$('body').unbind('keyup');

						// remove class
						$(this).parent().removeClass('selected');

						// hide
						$(id).hide('blind', {}, 'fast');
					}
				});

				// bind click outside
				$('body').bind('click', function(evt)
				{
					// unbind event
					$('body').unbind('click');

					// remove class
					$(this).parent().removeClass('selected');

					// hide
					$(id).hide('blind', {}, 'fast');
				});

				// add class
				$(this).parent().addClass('selected');

				// show
				$(id).show('blind', {}, 'fast');
			}
		})
	},


	// toggle between full width and sidebar-layout
	bindFullWidthSwitch: function()
	{
		$('#fullwidthSwitch a').toggle(
			function(evt)
			{
				// prevent default behaviour
				evt.preventDefault();

				// add class
				$(this).parent().addClass('collapsed');

				// toggle
				$('#subnavigation, #pagesTree').fadeOut(250);
			},
			function(evt)
			{
					// Stuff to do every *even* time the element is clicked;
					evt.preventDefault();

					// remove class
					$(this).parent().removeClass('collapsed');

					// toggle
					$('#subnavigation, #pagesTree').fadeIn(500);
			}
		);
	},


	// bind confirm message
	bindMassAction: function()
	{
		// set disabled
		$('.tableOptions .massAction select').addClass('disabled').prop('disabled', true);
		$('.tableOptions .massAction .submitButton').addClass('disabledButton').prop('disabled', true);

		// hook change events
		$('table input:checkbox').change(function(evt)
		{
			// get parent table
			var table = $(this).parents('table.dataGrid').eq(0);

			// any item checked?
			if(table.find('input:checkbox:checked').length > 0)
			{
				table.find('.massAction select').removeClass('disabled').prop('disabled', false);
				table.find('.massAction .submitButton').removeClass('disabledButton').prop('disabled', false);
			}

			// nothing checked
			else
			{
				table.find('.massAction select').addClass('disabled').prop('disabled', true);
				table.find('.massAction .submitButton').addClass('disabledButton').prop('disabled', true);
			}
		});

		// initialize
		$('.tableOptions .massAction option').each(function()
		{
			// get id
			var id = $(this).data('messageId');

			if(typeof id != 'undefined')
			{
				// initialize
				$('#'+ id).dialog(
				{
					autoOpen: false,
					draggable: false,
					resizable: false,
					modal: true,
					buttons: {
						'{$lblOK|ucfirst}': function()
						{
							// close dialog
							$(this).dialog('close');

							// submit the form
							$('select:visible option[data-message-id='+ $(this).attr('id') +']').parents('form').eq(0).submit();
						},
						'{$lblCancel|ucfirst}': function()
						{
							$(this).dialog('close');
						}
					},
					open: function(evt)
					{
						// set focus on first button
						if($(this).next().find('button').length > 0) { $(this).next().find('button')[0].focus(); }
					}
				});
			}
		});

		// hijack the form
		$('.tableOptions .massAction .submitButton').live('click', function(evt)
		{
			// prevent default action
			evt.preventDefault();

			// not disabled
			if(!$(this).is('.disabledButton'))
			{
				// get the selected element
				if($(this).parents('.massAction').find('select[name=action] option:selected').length > 0)
				{
					// get action element
					var element = $(this).parents('.massAction').find('select[name=action] option:selected');

					// if the rel-attribute exists we should show the dialog
					if(typeof element.data('messageId') != 'undefined')
					{
						// get id
						var id = element.data('messageId');

						// open dialog
						$('#'+ id).dialog('open');
					}

					// no confirm
					else $(this).parents('form').submit();
				}

				// no confirm
				else $(this).parents('form').submit();
			}
		});
	},


	// check all checkboxes with one checkbox in the tableheader
	bindMassCheckbox: function()
	{
		// mass checkbox changed
		$('th .checkboxHolder input:checkbox').bind('change', function(evt)
		{
			// check or uncheck all the checkboxes in this datagrid
			$(this).closest('table').find('td input:checkbox').prop('checked', $(this).is(':checked'));

			// set selected class
			if($(this).is(':checked')) $(this).parents().filter('table').eq(0).find('tbody tr').addClass('selected');
			else $(this).parents().filter('table').eq(0).find('tbody tr').removeClass('selected');
		});

		// single checkbox changed
		$('td.checkbox input:checkbox').bind('change', function(evt)
		{
			// check mass checkbox
			if($(this).closest('table').find('td.checkbox input:checkbox').length == $(this).closest('table').find('td.checkbox input:checkbox:checked').length)
			{
				$(this).closest('table').find('th .checkboxHolder input:checkbox').prop('checked', true);
			}

			// uncheck mass checkbox
			else{ $(this).closest('table').find('th .checkboxHolder input:checkbox').prop('checked', false); }
		});
	},


	bindPasswordGenerator: function() 
	{
		if($('.passwordGenerator').length > 0)
		{
			$('.passwordGenerator').passwordGenerator(
				{
					length: 8,
					numbers: false,
					lowercase: true,
					uppercase: true,
					generateLabel: '{$lblGenerate|ucfirst}'
				}
			);
		}
	},
	
	
	// bind the password strength meter to the correct inputfield(s)
	bindPasswordStrengthMeter: function()
	{
		if($('.passwordStrength').length > 0)
		{
			$('.passwordStrength').each(function()
			{
				// grab id
				var id = $(this).data('id');
				var wrapperId = $(this).attr('id');

				// hide all
				$('#'+ wrapperId +' p.strength').hide();

				// excecute function directly
				var classToShow = jsBackend.controls.checkPassword($('#'+ id).val());

				// show
				$('#'+ wrapperId +' p.'+ classToShow).show();

				// bind keypress
				$('#'+ id).live('keyup', function()
				{
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
	checkPassword: function(string)
	{
		// init vars
		var score = 0;
		var uniqueChars = [];

		// no chars means no password
		if(string.length == 0) return 'none';

		// less then 4 chars is just a weak password
		if(string.length <= 4) return 'weak';

		// loop chars and add unique chars
		for(var i = 0; i<string.length; i++)
		{
			if($.inArray(string.charAt(i), uniqueChars) == -1) { uniqueChars.push(string.charAt(i)); }
		}

		// less then 3 unique chars is just weak
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
	bindToggleDiv: function()
	{
		$('.toggleDiv').live('click', function(evt)
		{
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
	bindTableCheckbox: function()
	{
		// set classes
		$('tr td input:checkbox:checked').each(function() { $(this).parents().filter('tr').eq(0).addClass('selected'); });

		// bind change-events
		$('tr td input:checkbox').live('change', function(evt)
		{
			if($(this).is(':checked')) $(this).parents().filter('tr').eq(0).addClass('selected');
			else $(this).parents().filter('tr').eq(0).removeClass('selected');
		});
	},


	// bind target blank
	bindTargetBlank: function()
	{
		$('a.targetBlank').attr('target', '_blank');
	},


	// togle between the working languages
	bindWorkingLanguageSelection: function()
	{
		$('#workingLanguage').bind('change', function(evt)
		{
			// preventDefault
			evt.preventDefault();

			// break the url int parts
			var urlChunks = document.location.pathname.split('/');

			// get the querystring, we will append it later
			var queryChunks = document.location.search.split('&');
			var newChunks = [];

			// any parts in the querystring
			if(typeof queryChunks != 'undefined' && queryChunks.length > 0)
			{
				// remove variables that could trigger an message
				for(var i in queryChunks)
				{
					if(queryChunks[i].substring(0, 5) != 'token' &&
						queryChunks[i].substring(0, 5) != 'error' &&
						queryChunks[i].substring(0, 6) == 'report' &&
						queryChunks[i].substring(0, 3) == 'var' &&
						queryChunks[i].substring(0, 9) == 'highlight')
					{
						newChunks.push(queryChunks[i]);
					}
				}
			}

			// replace the third element with the new language
			urlChunks[2] = $(this).val();

			// remove action
			if(urlChunks.length > 4) urlChunks.pop();

			var url = urlChunks.join('/');
			if(newChunks.length > 0) url += '?token=true&' + newChunks.join('&');

			// rebuild the url and redirect
			document.location.href = url;
		});
	},


	// end
	eoo: true
}


/**
 * Backend effects
 *
 * @author	Dieter Vanden Eynde <dieter@dieterve.be>
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.effects =
{
	// init, something like a constructor
	init: function()
	{
		jsBackend.effects.bindHighlight();
	},


	// if a var highlight exists in the url it will be highlighted
	bindHighlight: function()
	{
		// get hightlight from url
	    var highlightId = utils.url.getGetValue('highlight');

	    // id is set
	    if(highlightId != '')
	    {
	    	// init selector of the element we want to highlight
	    	var selector = '#'+ highlightId;

	    	// item exists
	    	if($(selector).length > 0)
	    	{
		    	// if its a table row we need to highlight all cells in that row
		    	if($(selector)[0].tagName.toLowerCase() == 'tr'){ selector += ' td'; }

		    	// when we hover over the item we stop the effect, otherwise we will mess up background hover styles
	    		$(selector).bind('mouseover', function(){ $(selector).stop(true, true); });

		    	// highlight!
		    	$(selector).effect("highlight", {}, 5000);
	    	}
	    }
	},


	// end
	eoo: true
}


/**
 * Backend forms
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.forms =
{
	stringified: '',
		
	// init, something like a constructor
	init: function()
	{
		jsBackend.forms.placeholders();	// make sure this is done before focussing the first field
		jsBackend.forms.focusFirstField();
		jsBackend.forms.datefields();
		jsBackend.forms.submitWithLinks();
		jsBackend.forms.tagBoxes();
	},


	datefields: function()
	{
		var dayNames = ['{$locDayLongSun}', '{$locDayLongMon}', '{$locDayLongTue}', '{$locDayLongWed}', '{$locDayLongThu}', '{$locDayLongFri}', '{$locDayLongSat}'];
		var dayNamesMin = ['{$locDayShortSun}', '{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}'];
		var dayNamesShort = ['{$locDayShortSun}', '{$locDayShortMon}', '{$locDayShortTue}', '{$locDayShortWed}', '{$locDayShortThu}', '{$locDayShortFri}', '{$locDayShortSat}'];
		var monthNames = ['{$locMonthLong1}', '{$locMonthLong2}', '{$locMonthLong3}', '{$locMonthLong4}', '{$locMonthLong5}', '{$locMonthLong6}', '{$locMonthLong7}', '{$locMonthLong8}', '{$locMonthLong9}', '{$locMonthLong10}', '{$locMonthLong11}', '{$locMonthLong12}'];
		var monthNamesShort = ['{$locMonthShort1}', '{$locMonthShort2}', '{$locMonthShort3}', '{$locMonthShort4}', '{$locMonthShort5}', '{$locMonthShort6}', '{$locMonthShort7}', '{$locMonthShort8}', '{$locMonthShort9}', '{$locMonthShort10}', '{$locMonthShort11}', '{$locMonthShort12}'];
		
		$('.inputDatefieldNormal, .inputDatefieldFrom, .inputDatefieldTill, .inputDatefieldRange').datepicker(
		{
			dayNames: dayNames,
			dayNamesMin: dayNamesMin,
			dayNamesShort: dayNamesShort,
			hideIfNoPrevNext: true,
			monthNames: monthNames,
			monthNamesShort: monthNamesShort,
			nextText: '{$lblNext}',
			prevText: '{$lblPrevious}',
			showAnim: 'slideDown'
		});
		
		// the default, nothing special
		$('.inputDatefieldNormal').each(function()
		{
			// get data
			var data = $(this).data();
			var value = $(this).val();
			
			// set options
			$(this).datepicker('option', { 
				dateFormat: data.mask, 
				firstDate: data.firstday
			}).datepicker('setDate', value);
		});

		// datefields that have a certain startdate
		$('.inputDatefieldFrom').each(function()
		{
			// get data
			var data = $(this).data();
			var value = $(this).val();

			// set options
			$(this).datepicker('option', { 
				dateFormat: data.mask, firstDay: data.firstday,
				minDate: new Date(parseInt(data.startdate.split('-')[0], 10), parseInt(data.startdate.split('-')[1], 10) - 1, parseInt(data.startdate.split('-')[2], 10))
			}).datepicker('setDate', value);
		});

		// datefields that have a certain enddate
		$('.inputDatefieldTill').each(function()
		{
			// get data
			var data = $(this).data();
			var value = $(this).val();

			// set options
			$(this).datepicker('option', 
			{
				dateFormat: data.mask,
				firstDay: data.firstday,
				maxDate: new Date(parseInt(data.enddate.split('-')[0], 10), parseInt(data.enddate.split('-')[1], 10) -1, parseInt(data.enddate.split('-')[2], 10))
			}).datepicker('setDate', value);
		});

		// datefields that have a certain range
		$('.inputDatefieldRange').each(function()
		{
			// get data
			var data = $(this).data();
			var value = $(this).val();

			// set options
			$(this).datepicker('option', 
			{
				dateFormat: data.mask,
				firstDay: data.firstday,
				minDate: new Date(parseInt(data.startdate.split('-')[0], 10), parseInt(data.startdate.split('-')[1], 10) - 1, parseInt(data.startdate.split('-')[2], 10), 0, 0, 0, 0),
				maxDate: new Date(parseInt(data.enddate.split('-')[0], 10), parseInt(data.enddate.split('-')[1], 10) - 1, parseInt(data.enddate.split('-')[2], 10), 23, 59, 59)
			}).datepicker('setDate', value);
		});
	},


	// set the focus on the first field
	focusFirstField: function()
	{
		$('form input:visible:not(.noFocus):first').focus();
	},


	// set placeholders
	placeholders: function()
	{
		// detect if placeholder-attribute is supported
		jQuery.support.placeholder = ('placeholder' in document.createElement('input'));

		if(!jQuery.support.placeholder)
		{
			// bind focus
			$('input[placeholder]').focus(function()
			{
				// grab element
				var input = $(this);

				// only do something when the current value and the placeholder are the same
				if(input.val() == input.attr('placeholder'))
				{
					// clear
					input.val('');

					// remove class
					input.removeClass('placeholder');
				}
			});

			$('input[placeholder]').blur(function()
			{
				// grab element
				var input = $(this);

				// only do something when the input is empty or the value is the same as the placeholder
				if(input.val() == '' || input.val() == input.attr('placeholder'))
				{
					// set placeholder
					input.val(input.attr('placeholder'));

					// add class
					input.addClass('placeholder');
				}
			});

			// call blur to initialize
			$('input[placeholder]').blur();

			// hijack the form so placeholders aren't submitted as values
			$('input[placeholder]').parents('form').submit(function()
			{
				// find elements with placeholders
				$(this).find('input[placeholder]').each(function()
				{
					// grab element
					var input = $(this);

					// if the value and the placeholder are the same reset the value
					if(input.val() == input.attr('placeholder')) input.val('');
				});
			});
		}
	},

	// replaces buttons with <a><span>'s (to allow more flexible styling) and handle the form submission for them
	submitWithLinks: function()
	{
		// the html for the button that will replace the input[submit]
		var replaceHTML = '<a class="{class}" href="#{id}"><span>{label}</span></a>';

		// are there any forms that should be submitted with a link?
		if($('form.submitWithLink').length > 0)
		{
			$('form.submitWithLink').each(function()
			{
				// get id
				var formId = $(this).attr('id');
				var dontSubmit = false;

				// validate id
				if(formId != '')
				{
					// loop every button to be replaced
					$('form#'+ formId + '.submitWithLink input:submit').each(function()
					{
						$(this).after(replaceHTML.replace('{label}', $(this).val()).replace('{id}', $(this).attr('id')).replace('{class}', 'submitButton button ' + $(this).attr('class'))).css({ position:'absolute', top:'-9000px', left: '-9000px' }).attr('tabindex', -1);
					});

					// add onclick event for button (button can't have the name submit)
					$('form#'+ formId + ' a.submitButton').bind('click', function(evt)
					{
						evt.preventDefault();

						// is the button disabled?
						if($(this).prop('disabled')) return false;
						else $('form#'+ formId).submit();
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


	// add tagbox to the correct input fields
	tagBoxes: function()
	{
		if($('#sidebar input.tagBox').length > 0) 
		{ 
			$('#sidebar input.tagBox').tagBox(
				{ 
					emptyMessage: '{$msgNoTags|addslashes}', 
					errorMessage: '{$errAddTagBeforeSubmitting|addslashes}', 
					addLabel: '{$lblAdd|ucfirst}', 
					removeLabel: '{$lblDeleteThisTag|ucfirst}', 
					autoCompleteUrl: '/backend/ajax.php?module=tags&action=autocomplete&language={$LANGUAGE}' 
				}
			); 
		}
		if($('#leftColumn input.tagBox, #tabTags input.tagBox').length > 0) 
		{ 
			$('#leftColumn input.tagBox, #tabTags input.tagBox').tagBox(
				{ 
					emptyMessage: '{$msgNoTags|addslashes}', 
					errorMessage: '{$errAddTagBeforeSubmitting|addslashes}', 
					addLabel: '{$lblAdd|ucfirst}', 
					removeLabel: '{$lblDeleteThisTag|ucfirst}', 
					autoCompleteUrl: '/backend/ajax.php?module=tags&action=autocomplete&language={$LANGUAGE}', 
					showIconOnly: false 
				}
			);
		}
	},
	
	
	// show a warning when people are leaving the 
	unloadWarning: function() 
	{
		// only execute when there is a form on the page
		if($('form:visible').length > 0)
		{
			// loop fields
			$('form input, form select, form textarea').each(function() 
			{
				var $this = $(this);
				
				if(!$this.hasClass('dontCheckBeforeUnload'))
				{
					// store initial value
					$(this).data('initial-value', $(this).val()).addClass('checkBeforeUnload');
				}
			});

			// bind before unload, this will ask the user if he really wants to leave the page
			$(window).bind('beforeunload', jsBackend.forms.unloadWarningCheck);
			
			// if a form is submitted we don't want to ask the user if he wants to leave, we know for sure 
			$('form').bind('submit', function(evt) 
			{
				if(!evt.isDefaultPrevented()) $(window).unbind('beforeunload');
			});
		}
	},
	
	// check if any element has been changed
	unloadWarningCheck: function() 
	{
		// initialize var
		var changed = false;
		
		// save editors to the textarea-fields
		if(typeof tinyMCE != 'undefined') tinyMCE.triggerSave();
		
		// loop fields
		$('.checkBeforeUnload').each(function() 
		{
			// initialize
			var $this = $(this);
			
			// compare values
			if($this.data('initial-value') != $this.val())
			{
				// reset var
				changed = true;
				
				// stop looking
				return false;
			}
		});
		
		// not changed?
		if(!changed) {
			// prevent default
			/* 
			 * I know this line triggers errors, if you remove it the unload won't work anymore.
			 * Probably you'll fix this by passing the evt as an argument of the function, well this will break the functionality also.. 
			 * Uhu, a "wtf" is in place.. 
			 */
			evt.preventDefault();
			
			// unbind the event
			$(window).unbind('beforeunload');
		}

		// return if needed
		return (changed) ? '{$msgValuesAreChanged}' : null;
	},
	
	// end
	eoo: true
}


/**
 * Do custom layout/interaction stuff
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.layout =
{
	// init, something like a constructor
	init: function()
	{
		// hovers
		$('.contentTitle').hover(function() { $(this).addClass('hover'); }, function() { $(this).removeClass('hover'); });
		$('.dataGrid td a').hover(function() { $(this).parent().addClass('hover'); }, function() { $(this).parent().removeClass('hover'); });

		jsBackend.layout.showBrowserWarning();
		jsBackend.layout.dataGrid();
		if($('.datafilter').length > 0) jsBackend.layout.dataFilter();

		// fix last childs
		$('.options p:last').addClass('lastChild');
		if($('.dataFilter').length > 0) jsBackend.layout.dataFilter();
	},


	// dataFilter layout fixes
	dataFilter: function()
	{
		// add last child and first child for IE
		$('.dataFilter tbody td:first-child').addClass('firstChild');
		$('.dataFilter tbody td:last-child').addClass('lastChild');

		// init var
		var tallest = 0;

		// loop group
		$('.dataFilter tbody .options').each(function()
		{
			// taller?
			if($(this).height() > tallest) tallest = $(this).height();
		});

		// set new height
		$('.dataFilter tbody .options').height(tallest);
	},


	// datagrid layout
	dataGrid: function()
	{
		if(jQuery.browser.msie)
		{
			$('.dataGrid tr td:last-child').addClass('lastChild');
			$('.dataGrid tr td:first-child').addClass('firstChild');
		}

		// dynamic striping
		$('.dynamicStriping.dataGrid tr:nth-child(2n)').addClass('even');
		$('.dynamicStriping.dataGrid tr:nth-child(2n+1)').addClass('odd');
	},


	// if the browser isn't supported, show a warning
	showBrowserWarning: function()
	{
		var showWarning = false;

		// check firefox
		if(jQuery.browser.mozilla)
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0, 3).replace(/\./g, ''));

			// lower than 19?
			if(version < 19) { showWarning = true; }
		}

		// check opera
		if(jQuery.browser.opera)
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0, 1));

			// lower than 9?
			if(version < 9) { showWarning = true; }
		}

		// check safari, should be webkit when using 1.4
		if(jQuery.browser.safari)
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0, 3));

			// lower than 1.4?
			if(version < 400) { showWarning = true; }
		}

		// check IE
		if(jQuery.browser.msie)
		{
			// get version
			var version = parseInt(jQuery.browser.version.substr(0, 1));

			// lower or equal than 6
			if(version <= 6) { showWarning = true; }
		}

		// show warning if needed
		if(showWarning) { $('#showBrowserWarning').show(); }
	},


	// end
	eoo: true
}


/**
 * Handle form messages (action feedback: success, error, ...)
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.messages =
{
	timers: [],


	// init, something like a constructor
	init: function()
	{
		// bind close button
		$('#messaging .formMessage .iconClose').live('click', function(evt)
		{
			evt.preventDefault();
			jsBackend.messages.hide($(this).parents('.formMessage'));
		});
	},


	// hide a message
	hide: function(element)
	{
		// fade out
		element.fadeOut();
	},


	// add a new message into the que
	add: function(type, content)
	{
		var uniqueId = 'e'+ new Date().getTime().toString();
		var html = '<div id="'+ uniqueId +'" class="formMessage '+ type +'Message" style="display: none;">'+
					'	<p>'+ content +'</p>'+
					'	<div class="buttonHolderRight">'+
					'		<a class="button icon linkButton iconClose iconOnly" href="#"><span>X</span></a>'+
					'	</div>'+
					'</div>';

		// prepend
		$('#messaging').prepend(html);

		// show
		$('#'+ uniqueId).fadeIn();

		// timeout
		if(type == 'notice') { setTimeout('jsBackend.messages.hide($("#'+ uniqueId +'"));', 5000); }
		if(type == 'success') { setTimeout('jsBackend.messages.hide($("#'+ uniqueId +'"));', 5000); }
	},


	// end
	eoo: true
}


/**
 * Apply tabs
 *
 * @author	Jan Moessen <jan@netlash.com>
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.tabs =
{
	// init, something like a constructor
	init: function()
	{
		if($('.tabs').length > 0)
		{
			$('.tabs').tabs();

			$('.tabs .ui-tabs-panel').each(function()
			{
				if($(this).find('.formError').length > 0) {
					$($('.ui-tabs-nav a[href="#'+ $(this).attr('id') +'"]').parent()).addClass('ui-state-error');
				}
			});
		}

		$('.ui-tabs-nav a').click(function(e)
		{
			// if the browser supports history.pushState(), use it to update the URL with the fragment identifier, without triggering a scroll/jump
			if(window.history && window.history.pushState)
			{
				// an empty state object for now — either we implement a proper popstate handler ourselves, or wait for jQuery UI upstream
				window.history.pushState({}, document.title, this.getAttribute('href'));
			}

			// for browsers that do not support pushState
			else
			{
				// save current scroll height
				var scrolled = $(window).scrollTop();

				// set location hash
				window.location.hash = '#'+ this.getAttribute('href').split('#')[1];

				// reset scroll height
				$(window).scrollTop(scrolled);
			}
		});

		// select tab
		if($('.tabSelect').length > 0)
		{
			$('.tabSelect').live('click', function(evt)
			{
				// prevent default
				evt.preventDefault();
				$('.tabs').tabs('select', $(this).attr('href'));
			});
		}
	},


	// end
	eoo: true
}


/**
 * Apply TinyMCE
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.tinyMCE =
{
	// init, something like a constructor
	init: function()
	{
		$('.inputEditor').before('<div class="clickToEdit"><span>{$msgClickToEdit|addslashes}</span></div>');

		// bind click on the element
		$('.clickToEdit').live('click', function(evt)
		{
			// get id
			var id = $(this).siblings('textarea.inputEditor:first').attr('id');

			// validate id
			if(typeof id != undefined)
			{
				// show the toolbar
				$('#'+ id + '_external').show();

				// set focus to the editor
				tinyMCE.get(id).focus();
			}
		});
	},


	// format text (after retrieving it from the editor)
	afterSave: function(editor, object)
	{
		// create dom tree
		var $tmp = $('<div />').html(object.content);

		// remove target="_self"
		$tmp.find('a[target=_self]').removeAttr('target');

		// replace target="_blank" with class="targetBlank"
		$tmp.find('a[target=_blank]').addClass('targetBlank').removeAttr('target');

		// resave
		object.content = utils.string.xhtml($tmp.html());
	},


	// format text (before placing it in the editor)
	beforeLoad: function(editor, object)
	{
		// create dom tree
		var $tmp = $('<div />').html(object.content);

		// replace target="_blank" with class="targetBlank"
		$tmp.find('a.targetBlank').removeClass('targetBlank').attr('target', '_blank');

		// resave
		object.content = utils.string.xhtml($tmp.html());
	},


	// custom content checks
	checkContent: function(editor)
	{
		if(editor.isDirty())
		{
			var content = editor.getContent();
			var warnings = [];

			// no alt?
			if(content.match(/<img(.*)alt=""(.*)/im)) { warnings.push('{$msgEditorImagesWithoutAlt|addslashes}'); }

			// invalid links?
			if(content.match(/href="\/private\/([a-z]{2,})\/([a-z_]*)\/(.*)"/im)) { warnings.push('{$msgEditorInvalidLinks|addslashes}'); }

			// any warnings?
			if(warnings.length > 0)
			{
				if($('#' + editor.id + '_warnings').length > 0) $('#' + editor.id + '_warnings').html(warnings.join(' '));
				else $('#' + editor.id + '_parent').after('<span id="'+ editor.id + '_warnings' +'" class="infoMessage editorWarning">'+ warnings.join(' ') + '</span>');
			}

			// no warnings
			else $('#' + editor.id + '_warnings').remove();
		}
	},


	// end
	eoo: true
}


/**
 * Apply tooltip
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.tooltip =
{
	// init, something like a constructor
	init: function()
	{
		if($('.help').length > 0)
		{
			$('.help').tooltip({ effect: 'fade', relative: true }).dynamic();
		}
	},


	// end
	eoo: true
}


/**
 * Handle browsers with impaired CSS selector support
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.selectors =
{
	// init, something like a constructor
	init: function()
	{
		// missing CSS selector support IE6, IE7, IE8 as IE7
		if($.browser.msie && $.browser.version.substr(0, 1) < 9)
		{
			// nothing yet
		}
	},


	// end
	eoo: true
}


/**
 * Fix focus/blur events on impaired browsers
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.focusfix =
{
	// init, something like a constructor
	init: function()
	{
		function focusfix(selector, className)
		{
			$(selector).focus(function() { $(this).addClass(className); });
			$(selector).blur(function() { $(this).removeClass(className); });
		}

		// IE6 & IE7 focus fix
		if($.browser.msie && $.browser.version.substr(0, 1) < 9)
		{
			// apply focusfix
			focusfix('input.inputText', 'focus');
			focusfix('textarea', 'focus');
		}
	},


	// end
	eoo: true
}


/**
 * Enable setting of sequence by drag & drop
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.tableSequenceByDragAndDrop =
{
	// init, something like a constructor
	init: function()
	{
		if($('.sequenceByDragAndDrop tbody').length > 0)
		{
			$('.sequenceByDragAndDrop tbody').sortable(
			{
				items: 'tr',
				handle: 'td.dragAndDropHandle',
				placeholder: 'dragAndDropPlaceholder',
				forcePlaceholderSize: true,
				stop: function(event, ui)
				{
					// the table
					var table = $(this);
					var action = (typeof $(table.parents('table.dataGrid')).data('action') == 'undefined') ? 'sequence' : $(table.parents('table.dataGrid')).data('action').toString();

					// buil ajax-url
					var url = '/backend/ajax.php?module=' + jsBackend.current.module + '&action='+ action +'&language=' + jsBackend.current.language;

					// append
					if(typeof $(table.parents('table.dataGrid')).data('extra-params') != 'undefined') url += $(table.parents('table.dataGrid')).data('extra-params');

					// init var
					var rows = $(this).find('tr');
					var newIdSequence = new Array();

					// loop rowIds
					rows.each(function() { newIdSequence.push($(this).data('id')); });

					// make the call
					$.ajax(
					{
						url: url,
						data: 'new_id_sequence=' + newIdSequence.join(','),
						success: function(data, textStatus)
						{
							// not a succes so revert the changes
							if(data.code != 200)
							{
								// revert
								table.sortable('cancel');

								// show message
								jsBackend.messages.add('error', 'alter sequence failed.');
							}

							// redo odd-even
							table.find('tr').removeClass('odd').removeClass('even');
							table.find('tr:even').addClass('odd');
							table.find('tr:odd').addClass('even');

							// alert the user
							if(data.code != 200 && jsBackend.debug) { alert(data.message); }

							// show message
							jsBackend.messages.add('success', 'Changed order successfully.');
						},
						error: function(XMLHttpRequest, textStatus, errorThrown)
						{
							// init var
							var textStatus = 'alter sequence failed.';

							// get real message
							if(typeof XMLHttpRequest.responseText != 'undefined') textStatus = $.parseJSON(XMLHttpRequest.responseText).message;

							// show message
							jsBackend.messages.add('error', textStatus);

							// revert
							table.sortable('cancel');

							// alert the user
							if(jsBackend.debug) alert(textStatus);
						}
					});
				}
			});
		}
	},


	// end
	eoo: true
}


$(document).ready(jsBackend.init);
