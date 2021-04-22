export class Template {
  constructor () {
    // default position names
    this.defaultPositions = ['', 'main', 'left', 'right', 'top']

    // add first default position
    if ($('#position1').length === 0) this.addPosition()

    // first position can't be removed
    this.hideLastDeletePositionButton()

    // add handlers
    $(document).on('click', '.jsAddPosition', $.proxy(this.addPosition, this))
    $(document).on('click', '.jsAddBlock', this.addBlock)
    $(document).on('click', '.jsDeletePosition', $.proxy(this.deletePosition, this))
    $(document).on('click', '.jsDeleteBlock', this.deleteBlock)
  }

  hideLastDeletePositionButton () {
    const deletePositionButton = $('#positions .jsDeletePosition')

    if (deletePositionButton.length === 2) {
      deletePositionButton.last().hide()

      return
    }

    deletePositionButton.show()
  }

  /**
   * Add a block to a position
   */
  addBlock (e) {
    // prevent default event action
    e.preventDefault()

    // clone default extras dropdown
    const blockContainer = $('#type00').closest('.jsBlock').clone()

    // fetch position & block index
    const positionIndex = $(e.currentTarget).closest('.jsPosition').find('input[id^=position]').attr('id').replace('position', '')
    const blockIndex = $(e.currentTarget).closest('.jsBlocks').find('.jsBlock').length

    // update for id & name
    $('#type00', blockContainer).attr('id', 'type' + positionIndex + blockIndex).attr('name', 'type_' + positionIndex + '_' + blockIndex)

    // if there are no blocks yet prepend blocks container
    if (blockIndex === 0) {
      $(e.currentTarget).closest('.jsBlocks').prepend(blockContainer)
      return
    }

    // add to dom
    blockContainer.insertAfter($(e.currentTarget).closest('.jsBlocks').find('.jsBlock').last())
  }

  /**
   * Add a position
   */
  addPosition (e) {
    // prevent default event action
    if (e) e.preventDefault()

    // clone default position
    const positionContainer = $('#position0').closest('.jsPosition').clone()

    // set new index
    const index = $('#positions .jsPosition').length

    // update for (label), id & name (input)
    $('input[id^=position]', positionContainer).attr('id', 'position' + index).attr('name', 'position_' + index)
    $('label[for^=position]', positionContainer).attr('for', 'position' + index)

    // remove default blocks
    $('.jsBlocks > .jsBlock', positionContainer).remove()

    // update default name
    $('#position' + index, positionContainer).val(this.defaultPositions[index])

    // show position
    positionContainer.show()

    // add to dom
    positionContainer.insertAfter($('#positions .jsPosition:last'))

    this.hideLastDeletePositionButton()
  }

  /**
   * Delete a block in a position
   */
  deleteBlock (e) {
    // prevent default event action
    e.preventDefault()

    // get blocks container
    const blocksContainer = $(e.currentTarget).closest('.jsBlocks')

    // delete container
    $(e.currentTarget).closest('.jsBlock').remove()

    // loop all remaining blocks
    $('.jsBlock', blocksContainer).each((i, element) => {
      // fetch position & block index
      const positionIndex = $(element).closest('.jsPosition').prevAll('input[id^=position]').attr('id').replace('position', '')
      const blockIndex = $(element).prevAll('.jsBlock').length

      // update for id & name
      $('select[id^=type]', element).attr('id', 'type' + positionIndex + blockIndex).attr('name', 'type_' + positionIndex + '_' + blockIndex)
    })
  }

  /**
   * Delete a position
   */
  deletePosition (e) {
    // prevent default event action
    e.preventDefault()

    // get positions container
    const positionsContainer = $(e.currentTarget).closest('#positions')

    // delete container
    $(e.currentTarget).closest('.jsPosition').remove()

    // loop all remaining positions
    $('.jsPosition', positionsContainer).each((i, position) => {
      // fetch position index
      const positionIndex = i

      // update for (label), id & name (input)
      $('input[id^=position]', position).attr('id', 'position' + positionIndex).attr('name', 'position_' + positionIndex)
      $('label[for^=position]', position).attr('for', 'position' + positionIndex)

      // loop all blocks
      $('.jsBlock', this).each((i, block) => {
        // fetch block index
        const blockIndex = $(block).prevAll('.jsBlock').length

        // update for id & name
        $('select[id^=type]', block).attr('id', 'type' + positionIndex + blockIndex).attr('name', 'type_' + positionIndex + '_' + blockIndex)
      })
    })

    this.hideLastDeletePositionButton()
  }
}
