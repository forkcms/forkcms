if(!jsBackend) { var jsBackend = new Object(); }


/**
 * Interaction for the pages templates
 *
 * @author	Matthias Mullie <matthias@netlash.com>
 */
jsBackend.templates =
{
	// default position names
	defaultPositions: new Array('', 'main', 'left', 'right', 'top'),


	/**
	 * Kind of constructor
	 */
	init: function()
	{	
		// change template
		jsBackend.templates.changeTemplate();

		// add first default position
		if($('#position1').length == 0) jsBackend.templates.addPosition();

		// first position can't be removed
		$('#position1').parent().find('.deletePosition').remove();

		// add handlers
		$('#addPosition').live('click', jsBackend.templates.addPosition);
		$('.addBlock').live('click', jsBackend.templates.addBlock);
		$('.deletePosition').live('click', jsBackend.templates.deletePosition);
		$('.deleteBlock').live('click', jsBackend.templates.deleteBlock);
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
		var blockIndex = $(this).prevAll('div.defaultBlock').length + 1;

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
		$('#position' + index, positionContainer).val(jsBackend.templates.defaultPositions[index]);

		// show position
		positionContainer.show();

		// add to dom
		positionContainer.insertAfter($('#positionsList .position:last'));
	},


	/**
	 * Switch templates
	 */
	changeTemplate: function()
	{
		// bind change event
		$('#theme').change(function()
		{
			// redirect to page to display template overview of this theme
			window.location.search = '?theme=' + $(this).val();
		});
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
				var blockIndex = $(this).prevAll('div.defaultBlock').length + 1;

				// update for id & name
				$('select[id^=type]', this).attr('id', 'type' + positionIndex + blockIndex).attr('name', 'type_' + positionIndex + '_' + blockIndex);
			});
		});
	},


	eoo: true
}


$(document).ready(jsBackend.templates.init);