export class Collection {
  constructor () {
    var addField = '[data-addfield="collection"]'
    var removeField = '[data-removefield="collection"]'
    var sequenceField = '[data-role="sequence"]'
    var CollectionAdd = function (el) {
      $(el).on('click', addField, this.addField)
    }
    var CollectionRemove = function (el) {
      $(el).on('click', removeField, this.removeField)
    }
    var CollectionSequence = {
      init: function () {
        CollectionSequence.initSequence()
      },

      initSequence: function () {
        var $sequenceInstances = $('[data-role=collection-sequence]')

        if ($sequenceInstances.length === 0) {
          return
        }

        $.each($sequenceInstances, function (index, element) {
          var $sequenceBody = $(element)

          $sequenceBody.sortable(
            {
              items: '.list-group-item',
              handle: '[data-role="sequence-handle"]',
              placeholder: 'dragAndDropPlaceholder',
              forcePlaceholderSize: true,
              stop: function (e, ui) {
                CollectionSequence.saveNewSequence($(this).closest('[data-role=collection-sequence]'))
              }
            }
          )

          $sequenceBody.on('click.fork.order-move', '[data-role="order-move"]', function (e) {
            var $this = $(this)
            var $row = $this.closest('.list-group-item')
            var direction = $this.data('direction')

            if (direction === 'up') {
              $row.prev().insertAfter($row)
            }
            else if (direction === 'down') {
              $row.next().insertBefore($row)
            }

            CollectionSequence.saveNewSequence($(this).closest('[data-role=collection-sequence]'))
          })
        })
      },

      saveNewSequence: function ($sequenceBody) {
        var $counter = 0
        $.each($sequenceBody.find(sequenceField), function (index, element) {
          $(element).val($counter)
          $counter++
        })
      }
    }

    CollectionAdd.prototype.addField = function (e) {
      var $this = $(this)
      var selector = $this.attr('data-collection')
      var prototypeName = $this.attr('data-prototype-name')

      e && e.preventDefault()

      var collection = $('#' + selector)
      var list = collection.find('ul.js-collection').first()
      var count = list.find('> li').length

      var newWidget = collection.attr('data-prototype')

      // Check if an element with this ID already exists.
      // If it does, increase the count by one and try again
      var newName = newWidget.match(/id="(.*?)"/)
      var re = new RegExp(prototypeName, 'g')
      while ($('#' + newName[1].replace(re, count)).length > 0) {
        count++
      }
      newWidget = newWidget.replace(re, count)
      newWidget = newWidget.replace(/__id__/g, newName[1].replace(re, count))
      var newLi = $('<li class="list-group-item"></li>').html(newWidget)
      newLi.appendTo(list)
      CollectionSequence.saveNewSequence(newLi.closest(list))
      $this.trigger('collection-field-added', newLi)
    }

    CollectionRemove.prototype.removeField = function (e) {
      var $this = $(this)
      var parent = $this.closest('li').parent()

      e && e.preventDefault()

      $this.trigger('collection-field-removed')
      $this.trigger('collection-field-removed-before')
      $this.closest('li').remove()
      parent.trigger('collection-field-removed-after')
    }

    // Extra jquery functions
    // first get the old ones in case there is a conflict
    var oldAdd = $.fn.addField
    var oldRemove = $.fn.removeField

    $.fn.addField = function (option) {
      return this.each(function () {
        var $this = $(this)
        var data = $this.data('addfield')

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
        var $this = $(this)
        var data = $this.data('removefield')

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
