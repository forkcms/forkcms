/*!
 * jQuery Fork stuff
 */
/* global pageID */

/**
 * Meta-handler
 */
(function ($) {
  $.fn.doMeta = function (options) {
    // define defaults
    var defaults = {
      metaIdSelector: '#metaId',
      pageTitleSelector: '#pageTitle',
      pageTitleOverwriteSelector: '#pageTitleOverwrite',
      navigationTitleSelector: '#navigationTitle',
      navigationTitleOverwriteSelector: '#navigationTitleOverwrite',
      metaDescriptionSelector: '#metaDescription',
      metaDescriptionOverwriteSelector: '#metaDescriptionOverwrite',
      metaKeywordsSelector: '#metaKeywords',
      metaKeywordsOverwriteSelector: '#metaKeywordsOverwrite',
      urlSelector: '#url',
      urlOverwriteSelector: '#urlOverwrite',
      generatedUrlSelector: '#generatedUrl',
      baseFieldSelector: '#baseFieldName',
      customSelector: '#custom',
      classNameSelector: '#className',
      methodNameSelector: '#methodName',
      parametersSelector: '#parameters'
    }

    // extend options
    options = $.extend(defaults, options)

    // loop all elements
    return this.each(function () {
      // variables
      var $element = $(this)
      var $pageTitle = $(options.pageTitleSelector)
      var $pageTitleOverwrite = $(options.pageTitleOverwriteSelector)
      var $navigationTitle = $(options.navigationTitleSelector)
      var $navigationTitleOverwrite = $(options.navigationTitleOverwriteSelector)
      var $metaDescription = $(options.metaDescriptionSelector)
      var $metaDescriptionOverwrite = $(options.metaDescriptionOverwriteSelector)
      var $metaKeywords = $(options.metaKeywordsSelector)
      var $metaKeywordsOverwrite = $(options.metaKeywordsOverwriteSelector)
      var $urlOverwrite = $(options.urlOverwriteSelector)

      // bind keypress
      $element.bind('keyup input', utils.events.debounce(calculateMeta, 400))

      // bind change on the checkboxes
      if ($pageTitle.length > 0 && $pageTitleOverwrite.length > 0) {
        $pageTitleOverwrite.change(function (e) {
          if (!$element.is(':checked')) $pageTitle.val($element.val())
        })
      }

      if ($navigationTitle.length > 0 && $navigationTitleOverwrite.length > 0) {
        $navigationTitleOverwrite.change(function (e) {
          if (!$element.is(':checked')) $navigationTitle.val($element.val())
        })
      }

      $metaDescriptionOverwrite.change(function (e) {
        if (!$element.is(':checked')) $metaDescription.val($element.val())
      })

      $metaKeywordsOverwrite.change(function (e) {
        if (!$element.is(':checked')) $metaKeywords.val($element.val())
      })

      $urlOverwrite.change(function (e) {
        if (!$element.is(':checked')) generateUrl($element.val())
      })

      // generate url
      function generateUrl (url) {
        // make the call
        $.ajax(
          {
            data: {
              fork: {module: 'Core', action: 'GenerateUrl'},
              url: url,
              metaId: $(options.metaIdSelector).val(),
              baseFieldName: $(options.baseFieldSelector).val(),
              custom: $(options.customSelector).val(),
              className: $(options.classNameSelector).val(),
              methodName: $(options.methodNameSelector).val(),
              parameters: $(options.parametersSelector).val()
            },
            success: function (data, textStatus) {
              url = data.data
              $(options.urlSelector).val(url)
              $(options.generatedUrlSelector).html(url)
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
              url = utils.string.urlDecode(utils.string.urlise(url))
              $(options.urlSelector).val(url)
              $(options.generatedUrlSelector).html(url)
            }
          })
      }

      // calculate meta
      function calculateMeta (e, element) {
        var title = (typeof element !== 'undefined') ? element.val() : $(this).val()

        if ($pageTitle.length > 0 && $pageTitleOverwrite.length > 0) {
          if (!$pageTitleOverwrite.is(':checked')) $pageTitle.val(title)
        }

        if ($navigationTitle.length > 0 && $navigationTitleOverwrite.length > 0) {
          if (!$navigationTitleOverwrite.is(':checked')) $navigationTitle.val(title)
        }

        if (!$metaDescriptionOverwrite.is(':checked')) $metaDescription.val(title)

        if (!$metaKeywordsOverwrite.is(':checked')) $metaKeywords.val(title)

        if (!$urlOverwrite.is(':checked')) {
          if (typeof pageID === 'undefined' || pageID !== 1) {
            generateUrl(title)
          }
        }
      }
    })
  }
})(jQuery);

/**
 * Password generator
 */
(function ($) {
  $.fn.passwordGenerator = function (options) {
    // define defaults
    var defaults = {
      length: 6,
      uppercase: true,
      lowercase: true,
      numbers: true,
      specialchars: false,
      generateLabel: 'Generate'
    }

    // extend options
    options = $.extend(defaults, options)

    return this.each(function () {
      var id = $(this).attr('id')

      // append the button
      $(this).closest('.input-group input').after(
        '        <span class="input-group-btn"><button data-id="' + id + '" class="generatePasswordButton btn btn-default" type="button"><span>' + options.generateLabel + '</span></button></span>'
      )

      $('.generatePasswordButton').live('click', generatePassword)

      function generatePassword (e) {
        // prevent default
        e.preventDefault()

        var currentElement = $('#' + $(this).data('id'))

        // check if it isn't a text-element
        var newElement
        if (currentElement.attr('type') !== 'text') {
          // clone the current element
          newElement = $('<input value="" id="' + currentElement.attr('id') + '" name="' + currentElement.attr('name') + '" maxlength="' + currentElement.attr('maxlength') + '" class="' + currentElement.attr('class') + '" type="text">')

          // insert the new element
          newElement.insertBefore(currentElement)

          // remove the current one
          currentElement.remove()
        } else {
          // already a text element
          newElement = currentElement
        }

        // generate the password
        var pass = generatePass(options.length, options.uppercase, options.lowercase, options.numbers, options.specialchars)

        // set the generate password, and trigger the keyup event
        newElement.val(pass).keyup()
      }

      function generatePass (length, uppercase, lowercase, numbers, specialchars) {
        // the vowels
        var v = ['a', 'e', 'u', 'ae', 'ea']

        // the consonants
        var c = ['b', 'c', 'd', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'tr', 'cr', 'fr', 'dr', 'wr', 'pr', 'th', 'ch', 'ph', 'st']

        // the number-mapping
        var n = []
        n['a'] = 4
        n['b'] = 8
        n['e'] = 3
        n['g'] = 6
        n['l'] = 1
        n['o'] = 0
        n['s'] = 5
        n['t'] = 7
        n['z'] = 2

        // the special chars-mapping
        var s = []
        s['a'] = '@'
        s['i'] = '!'
        s['c'] = 'ç'
        s['s'] = '$'
        s['g'] = '&'
        s['h'] = '#'
        s['l'] = '|'
        s['x'] = '%'

        // init vars
        var pass = ''
        var tmp = ''

        // add a random consonant and vowel as longs as the length isn't reached
        for (var i = 0; i < length; i++) {
          tmp += c[Math.floor(Math.random() * c.length)] + v[Math.floor(Math.random() * v.length)]
        }

        // convert some chars to uppercase
        for (i = 0; i < length; i++) {
          if (Math.floor(Math.random() * 2)) {
            pass += tmp.substr(i, 1).toUpperCase()
          } else {
            pass += tmp.substr(i, 1)
          }
        }

        // numbers allowed?
        if (numbers) {
          tmp = ''
          for (i in pass) {
            // replace with a number if the random number can be devided by 3
            if (typeof n[pass[i].toLowerCase()] !== 'undefined' && (Math.floor(Math.random() * 4) % 3) === 1) {
              tmp += n[pass[i].toLowerCase()]
            } else {
              tmp += pass[i]
            }
          }
          pass = tmp
        }

        // special chars allowed
        if (specialchars) {
          tmp = ''
          for (i in pass) {
            // replace with a special number if the random number can be devided by 2
            if (typeof s[pass[i].toLowerCase()] !== 'undefined' && (Math.floor(Math.random() * 4) % 2)) {
              tmp += s[pass[i].toLowerCase()]
            } else {
              tmp += pass[i]
            }
          }
          pass = tmp
        }

        // if uppercase isn't allowed we convert all to lowercase
        if (!uppercase) pass = pass.toLowerCase()

        // if lowercase isn't allowed we convert all to uppercase
        if (!lowercase) pass = pass.toUpperCase()

        // return
        return pass
      }
    })
  }
})(jQuery);

/**
 * Inline editing
 */
(function ($) {
  $.fn.inlineTextEdit = function (options) {
    // define defaults
    var defaults = {
      params: {},
      current: {},
      extraParams: {},
      inputClasses: 'inputText',
      allowEmpty: false,
      tooltip: 'click to edit',
      afterSave: null
    }

    // extend options
    options = $.extend(defaults, options)

    // init var
    var editing = false

    // loop all elements
    return this.each(function () {
      // get current object
      var $this = $(this)

      // add wrapper and tooltip
      $this.html('<span>' + $this.text() + '</span><span style="display: none;" class="inlineEditTooltip label label-primary">' + options.tooltip + '</span>')

      // grab element
      var $span = $this.find('span')
      var element = $span.eq(0)
      var tooltip = $span.eq(1)

      // bind events
      element.bind('click focus', createElement)

      tooltip.bind('click', createElement)

      $this.hover(
        function () {
          if (element.hasClass('inlineEditing')) {
            $this.removeClass('inlineEditHover')
            tooltip.hide()
          } else {
            $this.addClass('inlineEditHover')
            tooltip.show()
          }
        },
        function () {
          $this.removeClass('inlineEditHover')
          tooltip.hide()
        }
      )

      // create an element
      function createElement () {
        // already editing
        if (editing) return

        // set var
        editing = true

        // grab current value
        options.current.value = element.html()

        // get current object
        var $this = $(this)

        // grab extra params
        if ($this.parent().data('id') !== '') {
          var extraParams = JSON.parse($this.parent().data('id').replace(/'/g, '"'))
          options.current.extraParams = extraParams
        }

        // add class
        element.addClass('inlineEditing')

        // hide label
        $this.removeClass('inlineEditHover')
        tooltip.hide()

        // remove events
        element.unbind('click').unbind('focus')

        // replacing quotes, less than and greater than with htmlentity, otherwise the inputfield is 'broken'
        options.current.value = utils.string.replaceAll(options.current.value, '"', '&quot;')

        // set html
        element.html('<input type="text" class="' + options.inputClasses + '" value="' + options.current.value + '" />')

        // store element
        options.current.element = $(element.find('input')[0])

        // set focus
        options.current.element.select()

        // bind events
        options.current.element.bind('blur', saveElement)
        options.current.element.keyup(function (e) {
          // handle escape
          if (e.which === 27) {
            // reset
            options.current.element.val(options.current.value)

            // destroy
            destroyElement()
          }

          // save when someone presses enter
          if (e.which === 13) saveElement()
        })
      }

      // destroy the element
      function destroyElement () {
        // get parent
        var parent = options.current.element.parent()

        // get value and replace quotes, less than and greater than with their htmlentities
        var newValue = options.current.element.val()
        newValue = utils.string.replaceAll(newValue, '"', '&quot;')
        newValue = utils.string.replaceAll(newValue, '<', '&lt;')
        newValue = utils.string.replaceAll(newValue, '>', '&gt;')

        // set HTML and rebind events
        parent.html(newValue).bind('click focus', createElement)

        // add class
        parent.removeClass('inlineEditing')

        // restore
        editing = false
      }

      // save the element
      function saveElement () {
        // if the new value is empty and that isn't allowed, we restore the original value
        if (!options.allowEmpty && options.current.element.val() === '') {
          options.current.element.val(options.current.value)
        }

        // is the value different from the original value
        if (options.current.element.val() !== options.current.value) {
          // add element to the params
          options.current.extraParams['value'] = options.current.element.val()

          // make the call
          $.ajax(
            {
              data: $.extend(options.params, options.current.extraParams),
              success: function (data, textStatus) {
                // call callback if it is a valid callback
                if (typeof options.afterSave === 'function') options.afterSave($this)

                // destroy the element
                destroyElement()
              },
              error: function (XMLHttpRequest, textStatus, errorThrown) {
                // reset
                options.current.element.val(options.current.value)

                // destroy the element
                destroyElement()

                // show message
                jsBackend.messages.add('danger', $.parseJSON(XMLHttpRequest.responseText).message)
              }
            })
        } else {
          // destroy the element
          destroyElement()
        }
      }
    })
  }
})(jQuery);

/**
 * key-value-box
 */
(function ($) {
  $.fn.keyValueBox = function (options) {
    // define defaults
    var defaults = {
      splitChar: ',',
      secondSplitChar: '|',
      emptyMessage: '',
      errorMessage: 'Add the item before submitting',
      addLabel: 'add',
      removeLabel: 'delete',
      params: {},
      showIconOnly: true,
      multiple: true
    }

    // extend options
    options = $.extend(defaults, options)

    // loop all elements
    return this.each(function () {
      // define some vars
      var id = $(this).attr('id')
      var elements = get()
      var blockSubmit = false
      var timer = null

      // reset label, so it points to the correct item
      $('label[for="' + id + '"]').attr('for', 'addValue-' + id)

      // bind submit
      $(this.form).submit(function (e) {
        // hide before..
        $('#errorMessage-' + id).remove()

        if (blockSubmit && $('#addValue-' + id).val().replace(/^\s+|\s+$/g, '') !== '') {
          // show warning
          $('#addValue-' + id).parents('.keyValueWrapper').append('<span style="display: none;" id="errorMessage-' + id + '" class="text-danger">' + options.errorMessage + '</span>')

          // clear other timers
          clearTimeout(timer)

          // we need the timeout otherwise the error is show every time the user presses enter in the keyvaluebox
          timer = setTimeout(function () { $('#errorMessage-' + id).show() }, 200)

          $('html, body').animate({
            scrollTop: ($('#errorMessage-' + id).parent().offset().top - 100)
          }, 500)
        }

        return !blockSubmit
      })

      // build replace html
      var html =
        '<div class="form-inline form-group keyValueWrapper">' +
        '    <div class="form-group input-group">' +
        '        <input class="form-control dontSubmit" id="addValue-' + id + '" name="addValue-' + id + '" type="text" />' +
        '        <span class="input-group-btn"><button type="button" id="addButton-' + id + '" class="btn btn-primary">' +
        '           <span class="fa fa-plus-square" aria-hidden="true"></span>' +
        '<span' + (options.showIconOnly ? ' class="sr-only"' : '') + '>' + options.addLabel + '</span>' +
        '        </button></span>' +
        '   </div>' +
        '</div>' +
        '<div class="form-inline form-group">' +
        '    <div id="elementList-' + id + '" class="form-group keyValueList">' +
        '    </div>' +
        '</div>'

      // hide current element
      $(this).css('visibility', 'hidden').css('position', 'absolute').css('top', '-9000px').css('left', '-9000px').attr('tabindex', '-1')

      // prepend html
      $(this).before(html)

      // add elements list
      build()

      // bind autocomplete if needed
      if (!$.isEmptyObject(options.params)) {
        $('#addValue-' + id).autocomplete(
          {
            delay: 200,
            minLength: 2,
            source: function (request, response) {
              $.ajax(
                {
                  data: $.extend(options.params, {term: request.term}),
                  success: function (data, textStatus) {
                    // init var
                    var realData = []

                    // alert the user
                    if (data.code !== 200 && jsBackend.debug) {
                      window.alert(data.message)
                    }

                    if (data.code === 200) {
                      for (var i in data.data) {
                        realData.push(
                          {
                            label: data.data[i].name,
                            value: data.data[i].value + options.secondSplitChar + data.data[i].name
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
      $('#addValue-' + id).bind('keyup', function (e) {
        blockSubmit = true

        // grab code
        var code = e.which

        // remove error message
        $('#errorMessage-' + id).remove()

        // enter of splitchar should add an element
        if (code === 13 || String.fromCharCode(code) === options.splitChar) {
          // hide before..
          $('#errorMessage-' + id).remove()

          // prevent default behaviour
          e.preventDefault()
          e.stopPropagation()

          // add element
          add()
        }

        // disable or enable button
        if ($(this).val().replace(/^\s+|\s+$/g, '') === '') {
          blockSubmit = false
          $('#addButton-' + id).addClass('disabledButton')
        } else {
          $('#addButton-' + id).removeClass('disabledButton')
        }
      })

      // bind click on add-button
      $('#addButton-' + id).bind('click', function (e) {
        // dont submit
        e.preventDefault()
        e.stopPropagation()

        // add element
        add()
      })

      // bind click on delete-button
      $('.deleteButton-' + id).live('click', function (e) {
        // dont submit
        e.preventDefault()
        e.stopPropagation()

        // remove element
        remove($(this).attr('rel'))
      })

      // add an element
      function add () {
        blockSubmit = false

        // init some vars
        var value = $('#addValue-' + id).val().replace(/^\s+|\s+$/g, '')
        var inElements = false

        // ugly hack to escape entities and quotes
        value = $('<div />').text(value).html().replace('"', '&quot;')

        // a value should contain the split char
        if (value.split(options.secondSplitChar).length === 1) value = ''

        // if multiple arguments aren't allowed, clear before adding
        if (!options.multiple) elements = []

        // reset box
        $('#addValue-' + id).val('').focus()
        $('#addButton-' + id).addClass('disabledButton')

        // remove error message
        $('#errorMessage-' + id).remove()

        // only add new element if it isn't empty
        if (value !== '') {
          // already in elements?
          for (var i in elements) {
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

      // build the list
      function build () {
        // init var
        var html = ''

        // no items and message given?
        if (elements.length === 0 && options.emptyMessage !== '') {
          html = '<p class="helpTxt">' + options.emptyMessage + '</p>'
        } else {
          // start html
          html = '<ul>'

          // loop elements
          for (var i in elements) {
            var humanValue = elements[i].split(options.secondSplitChar)[1]

            html += '    <li><span><strong>' + humanValue + '</strong>' + '        <button type="button" class="btn btn-danger deleteButton-' + id + '" rel="' + elements[i] + '" title="' + options.removeLabel + '">' + options.removeLabel + '</button></span>' + '    </li>'
          }

          // end html
          html += '</ul>'
        }

        // set html
        $('#elementList-' + id).html(html)
      }

      // get all items
      function get () {
        // get chunks
        var chunks = $('#' + id).val().split(options.splitChar)
        var elements = []
        var value = ''

        // loop elements and trim them from spaces
        for (var i in chunks) {
          value = chunks[i].replace(/^\s+|\s+$/g, '')
          if (value !== '') elements.push(value)
        }

        return elements
      }

      // remove an item
      function remove (value) {
        // get index for element
        var index = $.inArray(value, elements)

        // remove element
        if (index > -1) elements.splice(index, 1)

        // set new value
        $('#' + id).val(elements.join(options.splitChar))

        // rebuild element list
        build()
      }
    })
  }
})(jQuery);

/**
 * Tag-box
 */
(function ($) {
  $.fn.tagsBox = function (options) {
    // define defaults
    var defaults = {
      splitChar: ',',
      emptyMessage: '',
      errorMessage: 'Add the tag before submitting',
      addLabel: 'add',
      removeLabel: 'delete',
      params: {},
      canAddNew: false,
      showIconOnly: true,
      multiple: true
    }

    // extend options
    options = $.extend(defaults, options)

    // loop all elements
    return this.each(function () {
      // define some vars
      var id = $(this).attr('id')
      var elements = get()
      var blockSubmit = false
      var timer = null

      // reset label, so it points to the correct item
      $('label[for="' + id + '"]').attr('for', 'addValue-' + id)

      // bind submit
      $(this.form).submit(function (e) {
        // hide before..
        $('#errorMessage-' + id).remove()

        if (blockSubmit && $('#addValue-' + id).val().replace(/^\s+|\s+$/g, '') !== '') {
          // show warning
          $('#addValue-' + id).parents('.form-group').append('<span style="display: none;" id="errorMessage-' + id + '" class="formError">' + options.errorMessage + '</span>')

          // clear other timers
          clearTimeout(timer)

          // we need the timeout otherwise the error is show every time the user presses enter in the tagbox
          timer = setTimeout(function () { $('#errorMessage-' + id).show() }, 200)

          $('html, body').animate({
            scrollTop: ($('#errorMessage-' + id).parent().offset().top - 100)
          }, 500)
        }

        return !blockSubmit
      })

      // build replace html
      var html = '<div class="form-inline tagsWrapper">' +
        '    <div class="form-group">' +
        '        <div class="input-group">' +
        '           <input class="form-control dontSubmit" id="addValue-' + id + '" name="addValue-' + id + '" type="text" />' +
        '           <span class="input-group-btn"><button type="button" id="addButton-' + id + '" class="btn btn-default">' +
        '               <span class="fa fa-plus-square" aria-hidden="true"></span>' +
        '               <span' + (options.showIconOnly ? ' class="sr-only"' : '') + '>' + options.addLabel + '</span>' +
        '           </button></span>' +
        '        </div>' +
        '    </div>' +
        '    <div id="elementList-' + id + '" class="form-group tagList">' +
        '    </div>' +
        '</div>'

      // hide current element
      $(this).css('visibility', 'hidden').css('position', 'absolute').css('top', '-9000px').css('left', '-9000px').attr('tabindex', '-1')

      // prepend html
      $(this).before(html)

      // add elements list
      build()

      // bind autocomplete if needed
      if (!$.isEmptyObject(options.params)) {
        $('#addValue-' + id).autocomplete(
          {
            delay: 200,
            minLength: 2,
            source: function (request, response) {
              $.ajax(
                {
                  data: $.extend(options.params, {term: request.term}),
                  success: function (data, textStatus) {
                    // init var
                    var realData = []

                    // alert the user
                    if (data.code !== 200 && jsBackend.debug) {
                      window.alert(data.message)
                    }

                    if (data.code === 200) {
                      for (var i in data.data) {
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
      $('#addValue-' + id).bind('keyup', function (e) {
        blockSubmit = true

        // grab code
        var code = e.which

        // remove error message
        $('#errorMessage-' + id).remove()

        // enter of splitchar should add an element
        if (code === 13 || $(this).val().indexOf(options.splitChar) !== -1) {
          // hide before..
          $('#errorMessage-' + id).remove()

          // prevent default behaviour
          e.preventDefault()
          e.stopPropagation()

          // add element
          add()
        }

        // disable or enable button
        if ($(this).val().replace(/^\s+|\s+$/g, '') === '') {
          blockSubmit = false
          $('#addButton-' + id).addClass('disabledButton')
        } else {
          $('#addButton-' + id).removeClass('disabledButton')
        }
      })

      // bind click on add-button
      $('#addButton-' + id).bind('click', function (e) {
        // dont submit
        e.preventDefault()
        e.stopPropagation()

        // add element
        add()
      })

      // bind click on delete-button
      $('#elementList-' + id).on('click', '.deleteButton-' + id, function (e) {
        // dont submit
        e.preventDefault()
        e.stopPropagation()

        // remove element
        remove($(this).data('id'))
      })

      // add an element
      function add () {
        blockSubmit = false

        // init some vars
        var value = $('#addValue-' + id).val().replace(/^\s+|\s+$/g, '').replace(options.splitChar, '')
        var inElements = false

        // if multiple arguments aren't allowed, clear before adding
        if (!options.multiple) elements = []

        // reset box
        $('#addValue-' + id).val('').focus()
        $('#addButton-' + id).addClass('disabledButton')

        // remove error message
        $('#errorMessage-' + id).remove()

        // only add new element if it isn't empty
        if (value !== '') {
          // already in elements?
          for (var i in elements) {
            if (value === elements[i]) inElements = true
          }

          // only add if not already in elements
          if (!inElements) {
            // add elements
            elements.push(utils.string.stripForTag(value))

            // set new value
            $('#' + id).val(elements.join(options.splitChar))

            // rebuild element list
            build()
          }
        }
      }

      // build the list
      function build () {
        // init var
        var html = ''

        // no items and message given?
        if (elements.length === 0 && options.emptyMessage !== '') {
          html = '<p class="helpTxt">' + options.emptyMessage + '</p>'
        } else {
          // start html
          html = '<ul class="list-group">'

          // loop elements
          for (var i in elements) {
            var value = utils.string.stripForTag(elements[i])

            html += '    <li class="list-group-item">' +
              '        <button type="button" class="btn btn-danger btn-xs deleteButton-' + id + '" data-id="' + value + '" title="' + utils.string.stripForTag(options.removeLabel) + ' ' + value + '"><span class="fa fa-trash" aria-hidden="true"></span></button></span>' +
              '       <span><strong>' + value + '</strong>' +
              '    </li>'
          }

          // end html
          html += '</ul>'
        }

        // set html
        $('#elementList-' + id).html(html)
      }

      // get all items
      function get () {
        // get chunks
        var chunks = $('#' + id).val().split(options.splitChar)
        var elements = []
        var value = ''

        // loop elements and trim them from spaces
        for (var i in chunks) {
          value = chunks[i].replace(/^\s+|\s+$/g, '')
          if (value !== '') elements.push(value)
        }

        return elements
      }

      // remove an item
      function remove (value) {
        // get index for element
        var index = $.inArray(String(value), elements)

        // remove element
        if (index > -1) elements.splice(index, 1)

        // set new value
        $('#' + id).val(elements.join(options.splitChar))

        // rebuild element list
        build()
      }
    })
  }
})(jQuery);

/**
 * Multiple select box
 */
(function ($) {
  $.fn.multipleSelectbox = function (options) {
    // define defaults
    var defaults = {
      splitChar: ',',
      emptyMessage: '',
      addLabel: 'add',
      removeLabel: 'delete',
      showIconOnly: false,
      afterAdd: null,
      afterBuild: null,
      maxItems: null
    }

    // extend options
    options = $.extend(defaults, options)

    // loop all elements
    return this.each(function () {
      // define some vars
      var id = $(this).attr('id')
      var possibleOptions = $(this).find('option')
      var elements = get()
      var blockSubmit = false

      // bind submit
      $(this.form).submit(function () {
        return !blockSubmit
      })

      // remove previous HTML
      if ($('#elementList-' + id).length > 0) {
        $('#elementList-' + id).parent('.multipleSelectWrapper').remove()
      }

      // build replace html
      var html = '<div class="multipleSelectWrapper">' +
        '    <div id="elementList-' + id + '" class="multipleSelectList">' + '    </div>' +
        '    <div class="input-group">' +
        '        <select size="1" class="form-control select dontSubmit" id="addValue-' + id + '" name="addValue-' + id + '">'
      for (var i = 0; i < possibleOptions.length; i++) {
        html += '            <option value="' + $(possibleOptions[i]).attr('value') + '">' + $(possibleOptions[i]).html() + '</option>'
      }

      html += '        </select>' +
        '        <div class="input-group-btn">' +
        '            <button id="addButton-' + id + '" class="btn btn-success">' +
        '                <span class="fa fa-plus-square" aria-hidden="true"></span>' +
        '                <span' + (options.showIconOnly ? ' class="sr-only"' : '') + '>' + options.addLabel + '</span>' +
        '            </button>' +
        '        </div>' +
        '    </div>' +
        '</div>'

      // hide current element
      $(this).css('visibility', 'hidden').css('position', 'absolute').css('top', '-9000px').css('left', '-9000px').attr('tabindex', '-1')

      // prepend html
      $(this).before(html)

      // add elements list
      build()

      // bind click on add-button
      $('#addButton-' + id).bind('click', function (e) {
        // dont submit
        e.preventDefault()
        e.stopPropagation()

        if (options.maxItems !== null && elements.length >= options.maxItems) return

        // add element
        add()
      })

      // bind click on delete-button
      $('.deleteButton-' + id).live('click', function (e) {
        // dont submit
        e.preventDefault()
        e.stopPropagation()

        // remove element
        remove($(this).data('id'))
      })

      // add an element
      function add () {
        blockSubmit = false

        // init some vars
        var value = $('#addValue-' + id).val()
        var inElements = false

        // ugly hack to escape entities and quotes
        value = $('<div />').text(value).html().replace('"', '&quot;')

        // reset box
        $('#addValue-' + id).focus()

        // only add new element if it isn't empty
        if (value !== null && value !== '') {
          // already in elements?
          for (var i in elements) {
            if (value === elements[i]) inElements = true
          }

          // only add if not already in elements
          if (!inElements) {
            // add elements
            elements.push(value)

            // set new value
            $('#' + id).val(elements)

            // call callback if specified
            if (options.afterAdd !== null) { options.afterAdd(value) }

            // rebuild element list
            build()
          }
        }
      }

      // build the list
      function build () {
        // init var
        var html = ''

        // no items and message given?
        if (elements.length === 0 && options.emptyMessage !== '') {
          html = '<p class="helpTxt">' + options.emptyMessage + '</p>'
        } else {
          // start html
          html = '<ul class="list-group">'

          // loop elements
          for (var i in elements) {
            html += '    <li class="list-group-item">' +
              '        <button class="btn btn-danger btn-xs deleteButton-' + id + '" data-id="' + elements[i] + '" title="' + options.removeLabel + '">' +
              '            <span class="fa fa-trash" aria-hidden="true"></span><span class="sr-only">' + options.removeLabel + '</span>' +
              '        </button>' +
              '        <span><strong>' + $('#' + id + ' option[value="' + elements[i] + '"]').html() + '</strong></span>' +
              '    </li>'

            // remove from dropdown
            $('#addValue-' + id + ' option[value="' + elements[i] + '"]').prop('disabled', true)
          }

          // end html
          html += '</ul>'
        }

        // set html
        $('#elementList-' + id).html(html)

        // disabled?
        $('#addButton-' + id).removeClass('disabledButton')
        $('#addValue-' + id).removeClass('disabled').prop('disabled', false)
        if ($('#addValue-' + id + ' option:enabled').length === 0 || (options.maxItems !== null && elements.length >= options.maxItems)) {
          $('#addButton-' + id).addClass('disabledButton')
          $('#addValue-' + id).addClass('disabled').prop('disabled', true)
        }
        $('#addValue-' + id).val($('#addValue-' + id + ' option:enabled:first').attr('value'))

        // call callback if specified
        if (options.afterBuild !== null) { options.afterBuild(id) }
      }

      // get all items
      function get () {
        // get chunks
        var chunks = $('#' + id).val()
        var elements = []
        var value = ''

        if (!Array.isArray(chunks)) {
          chunks = [chunks]
        }

        // loop elements and trim them from spaces
        for (var i in chunks) {
          value = chunks[i].replace(/^\s+|\s+$/g, '')
          if (value !== '') elements.push(value)
        }

        return elements
      }

      // remove an item
      function remove (value) {
        // get index for element
        var index = $.inArray(value.toString(), elements)

        // remove element
        if (index > -1) elements.splice(index, 1)

        // set new value
        $('#' + id).val(elements)

        $('#addValue-' + id + ' option[value="' + value + '"]').prop('disabled', false)

        // rebuild element list
        build()
      }
    })
  }
})(jQuery);

/**
 * Multiple text box
 */
(function ($) {
  $.fn.multipleTextbox = function (options) {
    // define defaults
    var defaults = {
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
    return this.each(function () {
      // define some vars
      var id = $(this).attr('id')
      var elements = get()
      var blockSubmit = false
      var timer = null

      $('label[for="' + id + '"]').attr('for', 'addValue-' + id)

      // bind submit
      $(this.form).submit(function () {
        // hide before..
        $('#errorMessage-' + id).remove()

        if (blockSubmit && $('#addValue-' + id).val().replace(/^\s+|\s+$/g, '') !== '') {
          // show warning
          $('#addValue-' + id).parents('.form-group').append('<span style="display: none;" id="errorMessage-' + id + '" class="formError">' + options.errorMessage + '</span>')

          // clear other timers
          clearTimeout(timer)

          // we need the timeout otherwise the error is show every time the user presses enter in the keyvaluebox
          timer = setTimeout(function () { $('#errorMessage-' + id).show() }, 200)
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
      var html =
        '<div class="multipleTextWrapper">' +
        '<div id="elementList-' + id + '" class="multipleTextList">' +
        '</div>' +
        '<div class="form-group">' +
        '<div class="input-group">' +
        '<input class="form-control dontSubmit" id="addValue-' + id + '" name="addValue-' + id + '" type="text" />' +
        '<span class="input-group-btn"><button id="addButton-' + id + '" class="btn btn-success">' +
        '<span class="fa fa-plus-square" aria-hidden="true"></span>' +
        '<span' + (options.showIconOnly ? ' class="sr-only"' : '') + '>' + options.addLabel + '</span>' +
        '</button></span>' +
        '</div>' +
        '</div>' +
        '</div>'

      // hide current element
      $(this).css('visibility', 'hidden').css('position', 'absolute').css('top', '-9000px').css('left', '-9000px').attr('tabindex', '-1')

      // prepend html
      $(this).before(html)

      // add elements list
      build()

      // bind autocomplete if needed
      if (!$.isEmptyObject(options.params)) {
        $('#addValue-' + id).autocomplete(
          {
            delay: 200,
            minLength: 2,
            source: function (request, response) {
              $.ajax(
                {
                  data: $.extend(options.params, {term: request.term}),
                  success: function (data, textStatus) {
                    // init var
                    var realData = []

                    // alert the user
                    if (data.code !== 200 && jsBackend.debug) {
                      window.alert(data.message)
                    }

                    if (data.code === 200) {
                      for (var i in data.data) {
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
      $('#addValue-' + id).bind('keyup', function (e) {
        // block form submit
        blockSubmit = true

        // grab code
        var code = e.which

        // enter or splitchar should add an element
        if (code === 13 || $(this).val().indexOf(options.splitChar) !== -1) {
          // prevent default behaviour
          e.preventDefault()
          e.stopPropagation()

          // add element
          add()
        }

        // disable or enable button
        if ($(this).val().replace(/^\s+|\s+$/g, '') === '') {
          blockSubmit = false
          $('#addButton-' + id).addClass('disabledButton')
        } else {
          $('#addButton-' + id).removeClass('disabledButton')
        }
      })

      // bind click on add-button
      $('#addButton-' + id).bind('click', function (e) {
        // dont submit
        e.preventDefault()
        e.stopPropagation()

        // add element
        add()
      })

      // bind click on delete-button
      $(document).on('click', '.deleteButton-' + id, function (e) {
        // dont submit
        e.preventDefault()
        e.stopPropagation()

        // remove element
        remove($(this).data('id'))
      })

      // bind keypress on input fields (we need to rebuild so new values are saved)
      $(document).on('keyup', '.inputField-' + id, function (e) {
        // clear elements
        elements = []

        // loop
        $('.inputField-' + id).each(function () {
          // cleanup
          var value = $(this).val().replace(/^\s+|\s+$/g, '')

          // empty elements shouldn't be added
          if (value === '') {
            $(this).parent().parent().remove()
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
        var value = $('#addValue-' + id).val().replace(/^\s+|\s+$/g, '')
        var inElements = false

        // ugly hack to escape entities and quotes
        value = $('<div />').text(value).html().replace('"', '&quot;')

        // reset box
        if ($('#addValue-' + id).val().length > 0) {
          $('#addValue-' + id).val('').focus()
          $('#addButton-' + id).addClass('disabledButton')
        }

        // remove error message
        $('#errorMessage-' + id).remove()

        var values = value.split(options.splitChar)
        for (var e in values) {
          value = values[e]
          // only add new element if it isn't empty
          if (value !== '') {
            // already in elements?
            for (var i in elements) {
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
        var html = ''

        // no items and message given?
        if (elements.length === 0 && options.emptyMessage !== '') {
          html = '<p class="helpTxt">' + options.emptyMessage + '</p>'
        } else {
          // start html
          html = '<ul class="list-group">'

          // loop elements
          for (var i in elements) {
            html += '    <li class="list-group-item"><div class="input-group">' +
              '        <input class="form-control dontSubmit inputField-' + id + '" name="inputField-' + id + '[]" type="text" value="' + elements[i].replace('"', '&quot;') + '" />' +
              '        <span class="input-group-btn"><button class="btn btn-danger deleteButton-' + id + '" data-id="' + i + '" title="' + options.removeLabel + '">' +
              '           <span class="fa fa-trash" aria-hidden="true"></span>' +
              '            <span>' + options.removeLabel + '</span>' +
              '        </button></span>' +
              '    </div></li>'
          }

          // end html
          html += '</ul>'
        }

        // set html
        $('#elementList-' + id).html(html)

        // call callback if specified
        if (options.afterBuild !== null) { options.afterBuild(id) }
      }

      // get all items
      function get () {
        // get chunks
        var chunks = $('#' + id).val().split(options.splitChar)
        var elements = []
        var value = ''

        // loop elements and trim them from spaces
        for (var i in chunks) {
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
})(jQuery);

/**
 * Collection class
 */
(function ($) {
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
          } else if (direction === 'down') {
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
})(jQuery)
