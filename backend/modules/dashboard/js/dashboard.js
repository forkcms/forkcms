/**
 * Interaction for the dashboard module
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 * @author	Thomas Deceuninck <thomasdeceuninck@netlash.com>
 */
jsBackend.dashboard =
{
	itemOnTheMove: null,

	// init, something like a constructor
	init: function()
	{
		// variables
		$editDashboard = $('#editDashboard');
		$doneEditingDashboard = $('#doneEditingDashboard');
		$editDashboardClose = $('.editDashboardClose');

		$editDashboard.bind('click', jsBackend.dashboard.load);
		$doneEditingDashboard.bind('click', jsBackend.dashboard.save);
		$editDashboardClose.bind('click', jsBackend.dashboard.close);
	},

	close: function(evt)
	{
		// prevent default
		evt.preventDefault();

		// get widget
		$widget = $(this).parents('.sortableWidget').eq(0);

		if($widget.hasClass('isRemoved')) $widget.find('.options, .footer, .dataGridHolder').show().removeClass('isRemoved');
		else $widget.find('.options, .footer, .dataGridHolder').hide().addClass('isRemoved');
	},

	load: function(evt)
	{
		// prevent default
		evt.preventDefault();

		// variables
		$editDashboardMessage = $('#editDashboardMessage');
		$editDashboardClose = $('.editDashboardClose');
		$sortableWidget = $('.sortableWidget');
		$column = $('.column');

		// bind before unload event
		$(window).bind('beforeunload', function() {
			return '{$msgValuesAreChanged}';
		});

		// hide edit text
		$(this).hide();

		// show help text
		$editDashboardMessage.slideDown();

		// show close buttons
		$editDashboardClose.show();

		// show removed items
		$('.sortableWidget.isRemoved').show();

		$sortableWidget.each(function() {
			if($(this).find('.box').length == 0) $(this).remove();
		})

		// make them sortable
		$column.sortable(
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

		$sortableWidget.draggable(
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

		$sortableWidget.hover(
			function() { $(this).addClass('isDraggable'); },
			function() { $(this).removeClass('isDraggable'); }
		);
	},

	// save the changes
	save: function(evt)
	{
		// prevent default
		evt.preventDefault();

		// variables
		$editDashboard = $('#editDashboard');
		$editDashboardMessage = $('#editDashboardMessage');
		$editDashboardClose = $('.editDashboardClose');
		$column = $('.column');
		$sortableWidget = $('.sortableWidget');

		// unbind before unload event
		$(window).unbind('beforeunload');

		// show edit text
		$editDashboard.show();

		// hide help text
		$editDashboardMessage.slideUp();

		// hide close buttons
		$editDashboardClose.hide();

		// unbind
		$column.sortable('destroy');
		$sortableWidget.draggable('destroy').unbind('mouseenter mouseleave');

		// build new array
		var newSequence = new Array();

		// loop columns
		$column.each(function() {
			var items = new Array();

			// loop widgets
			$(this).find('.sortableWidget:visible').each(function()
			{
				// add item
				items.push({ module: $(this).data('module'), widget: $(this).data('widget'), hidden: $(this).hasClass('isRemoved'), present: true });
			});

			// add to all
			newSequence.push(items);
		});

		// hide removed
		$('.sortableWidget.isRemoved').hide();

		// make the call
		$.ajax(
		{
			data:
			{
				fork: { action: 'alter_sequence' },
				new_sequence: JSON.stringify(newSequence)
			},
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
	}
}

$(jsBackend.dashboard.init);