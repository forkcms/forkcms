export class MultiTextBox {
  static multipleTextbox (options, element) {
    // define defaults
    const defaults = {
      splitChar: ',',
      emptyMessage: '',
      addLabel: 'add',
      removeLabel: 'delete',
      errorMessage: 'Add the item before submitting',
      params: {},
      canAddNew: false,
      showIconOnly: false,
      afterBuild: null
    }

    // extend options
    options = $.extend(defaults, options)

    // loop all elements
    return $(element).each((index, el) => {
      // define some vars
      const id = $(el).attr('id')
      let elements = get()
      let blockSubmit = false
      let timer = null

      $('label[for="' + id + '"]').attr('for', 'addValue-' + id)

      // bind submit
      $(el.form).submit(() => {
        // hide before..
        $('#errorMessage-' + id).remove()

        if (blockSubmit && $('#addValue-' + id).val().replace(/^\s+|\s+$/g, '') !== '') {
          // show warning
          $('#addValue-' + id).parents('.form-group').append('<span id="errorMessage-' + id + '" class="invalid-feedback d-none">' + options.errorMessage + '</span>')

          // clear other timers
          clearTimeout(timer)

          // we need the timeout otherwise the error is show every time the user presses enter in the keyvaluebox
          timer = setTimeout(() => { $('#errorMessage-' + id).removeClass('d-none') }, 200)
          $('html, body').animate({
            scrollTop: ($('#errorMessage-' + id).parent().offset().top - 100)
          }, 500)
        }

        return !blockSubmit
      })

      // remove previous HTML
      if ($('#elementList-' + id).length > 0) {
        $('#elementList-' + id).parent('.multipleTextWrapper').remove()
      }

      // build replace html
      const html =
        '<div class="multipleTextWrapper">' +
        '<div id="elementList-' + id + '" class="multipleTextList">' +
        '</div>' +
        '<div class="form-group">' +
        '<div class="input-group">' +
        '<input class="form-control dontSubmit" id="addValue-' + id + '" name="addValue-' + id + '" type="text" />' +
        '<button id="addButton-' + id + '" class="btn btn-secondary' + (options.showIconOnly ? ' btn-icon-only' : '') + '">' +
        '<span class="fas fa-plus-square" aria-hidden="true"></span>' +
        '<span' + (options.showIconOnly ? ' class="visually-hidden"' : '') + '>' + options.addLabel + '</span>' +
        '</button>' +
        '</div>' +
        '</div>' +
        '</div>'

      // hide current element
      $(el).css('visibility', 'hidden').css('position', 'absolute').css('top', '-9000px').css('left', '-9000px').attr('tabindex', '-1')

      // prepend html
      $(el).before(html)

      // add elements list
      build()

      // bind autocomplete if needed
      if (!$.isEmptyObject(options.params)) {
        $('#addValue-' + id).autocomplete(
          {
            delay: 200,
            minLength: 2,
            source: (request, response) => {
              $.ajax(
                {
                  data: $.extend(options.params, {term: request.term}),
                  success: (data, textStatus) => {
                    // init var
                    const realData = []

                    // alert the user
                    if (data.code !== 200 && jsBackend.debug) {
                      window.alert(data.message)
                    }

                    if (data.code === 200) {
                      for (const i in data.data) {
                        realData.push(
                          {
                            label: data.data[i].name,
                            value: data.data[i].name
                          })
                      }
                    }

                    // set response
                    response(realData)
                  }
                })
            }
          })
      }

      // bind keypress on value-field
      $('#addValue-' + id).bind('keyup', (e) => {
        // block form submit
        blockSubmit = true

        // grab code
        const code = e.which

        // enter or splitchar should add an element
        if (code === 13 || $(e.currentTarget).val().indexOf(options.splitChar) !== -1) {
          // prevent default behaviour
          e.preventDefault()
          e.stopPropagation()

          // add element
          add()
        }

        // disable or enable button
        if ($(e.currentTarget).val().replace(/^\s+|\s+$/g, '') === '') {
          blockSubmit = false
          $('#addButton-' + id).addClass('disabledButton')
        } else {
          $('#addButton-' + id).removeClass('disabledButton')
        }
      })

      // bind click on add-button
      $('#addButton-' + id).bind('click', (e) => {
        // dont submit
        e.preventDefault()
        e.stopPropagation()

        // add element
        add()
      })

      // bind click on delete-button
      $(document).on('click', '.deleteButton-' + id, (e) => {
        // dont submit
        e.preventDefault()
        e.stopPropagation()

        // remove element
        remove($(e.currentTarget).data('id'))
      })

      // bind keypress on input fields (we need to rebuild so new values are saved)
      $(document).on('keyup', '.inputField-' + id, (e) => {
        // clear elements
        elements = []

        // loop
        $('.inputField-' + id).each((index, field) => {
          // cleanup
          const value = $(field).val().replace(/^\s+|\s+$/g, '')

          // empty elements shouldn't be added
          if (value === '') {
            $(field).parent().parent().remove()
          } else {
            elements.push(value)
          }
        })

        // set new value
        $('#' + id).val(elements.join(options.splitChar))
      })

      // add an element
      function add () {
        // unblock form submit
        blockSubmit = false

        // init some vars
        let value = $('#addValue-' + id).val().replace(/^\s+|\s+$/g, '')
        let inElements = false

        // ugly hack to escape entities and quotes
        value = $('<div />').text(value).html().replace('"', '&quot;')

        // reset box
        if ($('#addValue-' + id).val().length > 0) {
          $('#addValue-' + id).val('').focus()
          $('#addButton-' + id).addClass('disabledButton')
        }

        // remove error message
        $('#errorMessage-' + id).remove()

        const values = value.split(options.splitChar)
        for (const e in values) {
          value = values[e]
          // only add new element if it isn't empty
          if (value !== '') {
            // already in elements?
            for (const i in elements) {
              if (value === elements[i]) inElements = true
            }

            // only add if not already in elements
            if (!inElements) {
              // add elements
              elements.push(value)

              // set new value
              $('#' + id).val(elements.join(options.splitChar))

              // rebuild element list
              build()
            }
          }
        }
      }

      // build the list
      function build () {
        // init var
        let html = ''

        // no items and message given?
        if (elements.length === 0 && options.emptyMessage !== '') {
          html = '<p class="form-text text-muted">' + options.emptyMessage + '</p>'
        } else {
          // start html
          html = '<div>'

          // loop elements
          for (const i in elements) {
            html += '    <div class="form-group"><div class="input-group">' +
              '        <input class="form-control dontSubmit inputField-' + id + '" name="inputField-' + id + '[]" type="text" value="' + elements[i].replace('"', '&quot;') + '" />' +
              '        <button class="btn btn-danger deleteButton-' + id + '" data-id="' + i + '" title="' + options.removeLabel + '">' +
              '           <span class="fas fa-trash" aria-hidden="true"></span>' +
              '            <span>' + options.removeLabel + '</span>' +
              '        </button>' +
              '    </div></div>'
          }

          // end html
          html += '</div>'
        }

        // set html
        $('#elementList-' + id).html(html)

        // call callback if specified
        if (options.afterBuild !== null) { options.afterBuild(id) }
      }

      // get all items
      function get () {
        // get chunks
        const chunks = $('#' + id).val().split(options.splitChar)
        const elements = []
        let value = ''

        // loop elements and trim them from spaces
        for (const i in chunks) {
          value = chunks[i].replace(/^\s+|\s+$/g, '')
          if (value !== '') elements.push(value)
        }

        return elements
      }

      // remove an item
      function remove (index) {
        // remove element
        if (index > -1) elements.splice(index, 1)

        // set new value
        $('#' + id).val(elements.join(options.splitChar))

        // rebuild element list
        build()
      }
    })
  }
}
