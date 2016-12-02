/**
 * Interaction for the pages templates
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
        if($('#position1').length === 0) jsBackend.template.addPosition();

        // first position can't be removed
        $('#position1').closest('.jsPosition').find('.jsDeletePosition').remove();

        // add handlers
        $(document).on('click', '.jsAddPosition', jsBackend.template.addPosition);
        $(document).on('click', '.jsAddBlock', jsBackend.template.addBlock);
        $(document).on('click', '.jsDeletePosition', jsBackend.template.deletePosition);
        $(document).on('click', '.jsDeleteBlock', jsBackend.template.deleteBlock);
    },

    /**
     * Add a block to a position
     */
    addBlock: function(e)
    {
        // prevent default event action
        e.preventDefault();

        // clone default extras dropdown
        var blockContainer = $('#type00').closest('.jsBlock').clone();

        // fetch position & block index
        var positionIndex = $(this).closest('.jsPosition').find('input[id^=position]').attr('id').replace('position', '');
        var blockIndex = $(this).closest('.jsBlocks').find('.jsBlock').length;

        // update for id & name
        $('#type00', blockContainer).attr('id', 'type' + positionIndex + blockIndex).attr('name', 'type_' + positionIndex + '_' + blockIndex);

        // if there are no blocks yet prepend blocks container
        if (0 == blockIndex) {
            $(this).closest('.jsBlocks').prepend(blockContainer);
            return;
        }

        // add to dom
        blockContainer.insertAfter($(this).closest('.jsBlocks').find('.jsBlock').last());
    },

    /**
     * Add a position
     */
    addPosition: function(e)
    {
        // prevent default event action
        if(e) e.preventDefault();

        // clone default position
        var positionContainer = $('#position0').closest('.jsPosition').clone();

        // set new index
        var index = $('#positions .jsPosition').length;

        // update for (label), id & name (input)
        $('input[id^=position]', positionContainer).attr('id', 'position' + index).attr('name', 'position_' + index);
        $('label[for^=position]', positionContainer).attr('for', 'position' + index);

        // remove default blocks
        $('.jsBlocks > .jsBlock', positionContainer).remove();

        // update default name
        $('#position' + index, positionContainer).val(jsBackend.template.defaultPositions[index]);

        // show position
        positionContainer.show();

        // add to dom
        positionContainer.insertAfter($('#positions .jsPosition:last'));
    },

    /**
     * Delete a block in a position
     */
    deleteBlock: function(e)
    {
        // prevent default event action
        e.preventDefault();

        // get blocks container
        var blocksContainer = $(this).closest('.jsBlocks');

        // delete container
        $(this).closest('.jsBlock').remove();

        // loop all remaining blocks
        $('.jsBlock', blocksContainer).each(function(i)
        {
            // fetch position & block index
            var positionIndex = $(this).closest('.jsPosition').prevAll('input[id^=position]').attr('id').replace('position', '');
            var blockIndex = $(this).prevAll('.jsBlock').length;

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
        var positionsContainer = $(this).closest('#positions');

        // delete container
        $(this).closest('.jsPosition').remove();

        // loop all remaining positions
        $('.jsPosition', positionsContainer).each(function(i)
        {
            // fetch position index
            var positionIndex = i;

            // update for (label), id & name (input)
            $('input[id^=position]', this).attr('id', 'position' + positionIndex).attr('name', 'position_' + positionIndex);
            $('label[for^=position]', this).attr('for', 'position' + positionIndex);

            // loop all blocks
            $('.jsBlock', this).each(function(i)
            {
                // fetch block index
                var blockIndex = $(this).prevAll('.jsBlock').length;

                // update for id & name
                $('select[id^=type]', this).attr('id', 'type' + positionIndex + blockIndex).attr('name', 'type_' + positionIndex + '_' + blockIndex);
            });
        });
    }
};

$(jsBackend.template.init);
