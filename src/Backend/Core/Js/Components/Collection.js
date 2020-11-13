import Sortable from 'sortablejs'

export class Collection {
  constructor () {
    const addField = '[data-addfield="collection"]'
    const removeField = '[data-removefield="collection"]'
    const sequenceField = '[data-role="sequence"]'
    const CollectionAdd = function (el) {
      $(el).on('click', addField, this.addField)
    }
    const CollectionRemove = function (el) {
      $(el).on('click', removeField, this.removeField)
    }
    const CollectionSequence = {
      init: function () {
        CollectionSequence.initSequence()
      },

      initSequence: function () {
        const $sequenceInstances = $('[data-role=collection-sequence]')

        if ($sequenceInstances.length === 0) {
          return
        }

        $.each($sequenceInstances, function (index, element) {
          /* eslint-disable no-new */
          new Sortable(element, {
            group: typeof $sequenceInstances.attr('data-sequence-group') !== 'undefined' ? $sequenceInstances.data('sequence-group') : '',

            // Element dragging ended
            onEnd: function (event) {
              const draggedItem = $(event.item)
              CollectionSequence.saveNewSequence(draggedItem.closest('[data-role=collection-sequence]'))
              CollectionSequence.saveNewGroup(event)
            }
          })

          $(element).on('click.fork.order-move', '[data-role="order-move"]', function (e) {
            const $this = $(this)
            const $row = $this.closest('.list-group-item')
            const direction = $this.data('direction')

            if (direction === 'up') {
              $row.prev().insertAfter($row)
            } else if (direction === 'down') {
              $row.next().insertBefore($row)
            }

            CollectionSequence.saveNewSequence($(this).closest('[data-role=collection-sequence]'))
          })
        })
      },

      getFieldIndexFromString: function (name) {
        const chunks = name.split('_')

        for (const chunk of chunks) {
          if (!isNaN(+chunk)) {
            return (+chunk)
          }
        }
        return -1
      },

      saveNewSequence: function ($sequenceBody) {
        let counter = 0
        $.each($sequenceBody.find(sequenceField), function (index, element) {
          $(element).val(counter)
          counter++
        })
      },

      buildNewString: function (oldString, fromBlockName, toBlockName, currentIndex, newIndex, prefix = '', suffix = '') {
        let searchFor = fromBlockName
        let replaceWith = toBlockName
        if (fromBlockName !== toBlockName) {
          searchFor += prefix + currentIndex + suffix
          replaceWith += prefix + newIndex + suffix
        }

        return oldString.replace(searchFor, replaceWith)
      },

      saveNewGroup: function (event) {
        const $this = this
        const fromBlockName = $(event.from).data('position')
        const toBlockName = $(event.to).data('position')
        let currentFieldIndex = null
        let newFieldIndex = event.newIndex
        const $sequenceField = $(event.item).find(sequenceField)
        const currentId = $sequenceField.attr('id')

        if (fromBlockName !== toBlockName) {
          currentFieldIndex = this.getFieldIndexFromString(currentId)
          if (currentFieldIndex === -1) {
            console.error('Could not find the index.')
          }
          const regexp = new RegExp(fromBlockName + '_' + currentFieldIndex, 'g')
          while ($('#' + currentId.replace(regexp, toBlockName + '_' + newFieldIndex)).length > 0) {
            newFieldIndex++
          }
        }

        $(event.item).find('[id*="' + fromBlockName + '"]').each(function (index, item) {
          const oldId = $(item).attr('id')
          const newId = $this.buildNewString(oldId, fromBlockName, toBlockName, currentFieldIndex, newFieldIndex, '_')

          $(item).attr('id', newId)
        })

        $(event.item).find('[aria-labelledby*="' + fromBlockName + '"]').each(function (index, item) {
          const oldLabel = $(item).attr('aria-labelledby')
          const newLabel = $this.buildNewString(oldLabel, fromBlockName, toBlockName, currentFieldIndex, newFieldIndex, '_')

          $(item).attr('aria-labelledby', newLabel)
        })

        $(event.item).find('[name*="' + fromBlockName + '"]').each(function (index, item) {
          const oldName = $(item).attr('name')
          const newName = $this.buildNewString(oldName, fromBlockName, toBlockName, currentFieldIndex, newFieldIndex, '][', ']')

          $(item).attr('name', newName)
        })

        $('[data-position="' + toBlockName + '"] li.list-group-item').each(function (index, item) {
          $(item).find(sequenceField).val(index)
        })
      }
    }

    CollectionAdd.prototype.addField = function (e) {
      const $this = $(this)
      const selector = $this.attr('data-collection')
      const prototypeName = $this.attr('data-prototype-name')

      e && e.preventDefault()

      const collection = $('#' + selector)
      const list = collection.find('ul.js-collection').first()
      let count = list.find('> li').length

      let newWidget = collection.attr('data-prototype')

      // Check if an element with this ID already exists.
      // If it does, increase the count by one and try again
      const newName = newWidget.match(/id="(.*?)"/)
      const re = new RegExp(prototypeName, 'g')
      while ($('#' + newName[1].replace(re, count)).length > 0) {
        count++
      }
      newWidget = newWidget.replace(re, count)
      newWidget = newWidget.replace(/__id__/g, newName[1].replace(re, count))
      const newLi = $('<li class="list-group-item"></li>').html(newWidget)
      newLi.appendTo(list)
      CollectionSequence.saveNewSequence(newLi.closest(list))
      $this.trigger('collection-field-added', newLi)
    }

    CollectionRemove.prototype.removeField = function (e) {
      const $this = $(this)
      const parent = $this.closest('li').parent()

      e && e.preventDefault()

      $this.trigger('collection-field-removed')
      $this.trigger('collection-field-removed-before')
      $this.closest('li').remove()
      parent.trigger('collection-field-removed-after')
    }

    // Extra jquery functions
    // first get the old ones in case there is a conflict
    const oldAdd = $.fn.addField
    const oldRemove = $.fn.removeField

    $.fn.addField = function (option) {
      return this.each(function () {
        const $this = $(this)
        let data = $this.data('addfield')

        if (!data) {
          $this.data('addfield', (data = new CollectionAdd(this)))
        }
        if (typeof option === 'string') {
          data[option].call($this)
        }
      })
    }

    $.fn.removeField = function (option) {
      return this.each(function () {
        const $this = $(this)
        let data = $this.data('removefield')

        if (!data) {
          $this.data('removefield', (data = new CollectionRemove(this)))
        }
        if (typeof option === 'string') {
          data[option].call($this)
        }
      })
    }

    $.fn.addField.Constructor = CollectionAdd
    $.fn.removeField.Constructor = CollectionRemove

    $.fn.addField.noConflict = function () {
      $.fn.addField = oldAdd
      return this
    }
    $.fn.removeField.noConflict = function () {
      $.fn.removeField = oldRemove
      return this
    }

    $(document).on('click.addfield.data-api', addField, CollectionAdd.prototype.addField)
    $(document).on('click.removefield.data-api', removeField, CollectionRemove.prototype.removeField)

    $(CollectionSequence.init)
  }
}
