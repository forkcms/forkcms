if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the dashboard module
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
jsBackend.dashboard =
{
	itemOnTheMove: null,
		
	// init, something like a constructor
	init: function()
	{
		$('#editDashboard').click(jsBackend.dashboard.load);
		$('#doneEditingDashboard').click(jsBackend.dashboard.save);
		$('.editDashboardClose').click(jsBackend.dashboard.close);
	},


	close: function(evt) {
		// prevent default
		evt.preventDefault();

		// get widget
		var widget = $(this).parents('.sortableWidget').eq(0);
		
		if(widget.hasClass('isRemoved'))
		{
			$(widget.find('.options, .footer, .dataGridHolder')).show();
			widget.removeClass('isRemoved');
		}
		else
		{
			$(widget.find('.options, .footer, .dataGridHolder')).hide();
			widget.addClass('isRemoved');
		}
	},
	

	load: function(evt)
	{
		// prevent default
		evt.preventDefault();
		
		// bind before unload event
		$(window).bind('beforeunload', function() {
			return '{$msgValuesAreChanged}';
		});		
		
		// hide edit text
		$(this).hide();
		
		// show help text
		$('#editDashboardMessage').slideDown();
		
		// show close buttons
		$('.editDashboardClose').show();
		
		// show removed items
		$('.sortableWidget.isRemoved').show();
		
		$('.sortableWidget').each(function() {
			if($(this).find('.box').length == 0) $(this).remove();
		})

		// make them sortable
		$('.column').sortable(
			{
				connectWith: '.column', 
				forceHelperSize: true,
				forcePlaceholderSize: true,
				placeholder: 'dragAndDropPlaceholder',
				stop: function(event, ui) 
				{
					// remove the original item
					jsBackend.dashboard.itemOnTheMove.hide();
				}
			}
		);
		
		$('.sortableWidget').draggable(
			{ 
				cursor: 'move',
				connectToSortable: '.column',
				helper: 'clone',
				opacity: 0.50, 
				revert: 'invalid',
				start: function(event, ui) 
					{
						// set placeholders height
						$('.dragAndDropPlaceholder').css('height', $(this).height().toString() + 'px');

						// store
						jsBackend.dashboard.itemOnTheMove = $(this);
					}
			}
		);
		
		$('.sortableWidget').hover(
			function() { $(this).addClass('isDraggable'); },
			function() { $(this).removeClass('isDraggable'); }
		);
	},
	
	
	// save the changes 
	save: function(evt) 
	{
		// prevent default
		evt.preventDefault();
		
		// unbind before unload event
		$(window).unbind('beforeunload');
		
		// show edit text
		$('#editDashboard').show();
		
		// hide help text
		$('#editDashboardMessage').slideUp();

		// hide close buttons
		$('.editDashboardClose').hide();

		// unbind
		$('.column').sortable('destroy');
		$('.sortableWidget').draggable('destroy');
		$('.sortableWidget').unbind('mouseenter mouseleave');
		
		// build new array
		var newSequence = new Array();
		
		// loop columns
		$('.column').each(function() {
			var items = new Array();
			
			// loop widgets
			$(this).find('.sortableWidget:visible').each(function() {
				// add item
				items.push({ module: $(this).data('module'), widget: $(this).data('widget'), hidden: $(this).hasClass('isRemoved'), present: true });
			});
			
			// add to all
			newSequence.push(items);
		})
		
		// hide removed
		$('.sortableWidget.isRemoved').hide();
		
		// make the call
		$.ajax(
		{
			url: '/backend/ajax.php?module=dashboard&action=alter_sequence&language=' + jsBackend.current.language,
			data: 'new_sequence=' + JSON.stringify(newSequence),
			success: function(data, textStatus)
			{
				// not a succes so revert the changes
				if(data.code != 200)
				{
					// refresh page
					// window.location.reload(true);
				}

				// show message
				jsBackend.messages.add('success', data.message);
				
				if(data.data.reload) 
				{
					setTimeout('window.location.reload(true)', 2000);
				}
				
			},
			error: function(XMLHttpRequest, textStatus, errorThrown)
			{
				// show message
				jsBackend.messages.add('error', 'alter sequence failed.');

				// refresh page
				// window.location.reload(true);

				// alert the user
				if(jsBackend.debug) alert(textStatus);
			}
		});
	},


	// end
	eoo: true
}


$(document).ready(jsBackend.dashboard.init);