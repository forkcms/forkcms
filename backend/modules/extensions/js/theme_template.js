/**
 * Interaction for the pages templates
 *
 * @author	Matthias Mullie <forkcms@mullie.eu>
 */
jsBackend.template =
{
	// default position names
	defaultPositions: new Array('', 'main', 'left', 'right', 'top'),

	/**
	 * Kind of constructor
	 */
	init: function()
	{
		// add first default position
		if($('#position1').length == 0) jsBackend.template.addPosition();

		// first position can't be removed
		$('#position1').parent().find('.deletePosition').remove();

		// add handlers
		$(document).on('click', '#addPosition', jsBackend.template.addPosition);
		$(document).on('click', '.addBlock', jsBackend.template.addBlock);
		$(document).on('click', '.deletePosition', jsBackend.template.deletePosition);
		$(document).on('click', '.deleteBlock', jsBackend.template.deleteBlock);
	},

	/**
	 * Add a block to a position
	 */
	addBlock: function(e)
	{
		// prevent default event action
		e.preventDefault();

		// clone default extras dropdown
		var blockContainer = $('#type00').parent().clone();

		// fetch position & block index
		var positionIndex = $(this).parent().prevAll('input[id^=position]').attr('id').replace('position', '');
		var blockIndex = $(this).prevAll('div.defaultBlock').length;

		// update for id & name
		$('#type00', blockContainer).attr('id', 'type' + positionIndex + blockIndex).attr('name', 'type_' + positionIndex + '_' + blockIndex);

		// add to dom
		blockContainer.insertBefore($(this));
	},

	/**
	 * Add a position
	 */
	addPosition: function(e)
	{
		// prevent default event action
		if(e) e.preventDefault();

		// clone default position
		var positionContainer = $('#position0').parent().clone();

		// set new index
		var index = $('#positionsList .position').length;

		// update for (label), id & name (input)
		$('input[id^=position]', positionContainer).attr('id', 'position' + index).attr('name', 'position_' + index);
		$('label[for^=position]', positionContainer).attr('for', 'position' + index);

		// remove default blocks
		$('.defaultBlocks > *:not(a.addBlock)', positionContainer).remove();

		// update default name
		$('#position' + index, positionContainer).val(jsBackend.template.defaultPositions[index]);

		// show position
		positionContainer.show();

		// add to dom
		positionContainer.insertAfter($('#positionsList .position:last'));
	},

	/**
	 * Delete a block in a position
	 */
	deleteBlock: function(e)
	{
		// prevent default event action
		e.preventDefault();

		// get blocks container
		var blocksContainer = $(this).parent().parent();

		// delete container
		$(this).parent().remove();

		// loop all remaining blocks
		$('.defaultBlock', blocksContainer).each(function(i)
		{
			// fetch position & block index
			var positionIndex = $(this).parent().prevAll('input[id^=position]').attr('id').replace('position', '');
			var blockIndex = $(this).prevAll('div.defaultBlock').length;

			// update for id & name
			$('select[id^=type]', this).attr('id', 'type' + positionIndex + blockIndex).attr('name', 'type_' + positionIndex + '_' + blockIndex);
		});
	},

	/**
	 * Delete a position
	 */
	deletePosition: function(e)
	{
		// prevent default event action
		e.preventDefault();

		// get positions container
		var positionsContainer = $(this).parent().parent().parent();

		// delete container
		$(this).parent().parent().remove();

		// loop all remaining positions
		$('.position', positionsContainer).each(function(i)
		{
			// fetch position index
			var positionIndex = i;

			// update for (label), id & name (input)
			$('input[id^=position]', this).attr('id', 'position' + positionIndex).attr('name', 'position_' + positionIndex);
			$('label[for^=position]', this).attr('for', 'position' + positionIndex);

			// loop all blocks
			$('.defaultBlock', this).each(function(i)
			{
				// fetch block index
				var blockIndex = $(this).prevAll('div.defaultBlock').length;

				// update for id & name
				$('select[id^=type]', this).attr('id', 'type' + positionIndex + blockIndex).attr('name', 'type_' + positionIndex + '_' + blockIndex);
			});
		});
	}
}

$(jsBackend.template.init);
