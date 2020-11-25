import { StringUtil } from './StringUtil'

export class InlineTextEdit {
  static inlineTextEdit (options, element) {
    // define defaults
    const defaults = {
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
    let editing = false

    // loop all elements
    return $(element).each((index, el) => {
      // get current object
      const $this = $(el)

      // add wrapper and tooltip
      $this.html('<span>' + $this.html() + '</span><span style="display: none;" class="inlineEditTooltip label label-primary">' + options.tooltip + '</span>')

      // grab element
      const $span = $this.find('span')
      const element = $span.eq(0)
      const tooltip = $span.eq(1)

      // bind events
      element.bind('click focus', createElement)

      tooltip.bind('click', createElement)

      $this.hover(
        () => {
          if (element.hasClass('inlineEditing')) {
            $this.removeClass('inlineEditHover')
            tooltip.hide()
          } else {
            $this.addClass('inlineEditHover')
            tooltip.show()
          }
        },
        () => {
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
        const $this = $(this)

        // grab extra params
        if ($this.parent().data('id') !== '') {
          const extraParams = JSON.parse($this.parent().data('id').replace(/'/g, '"'))
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
        options.current.value = StringUtil.replaceAll(options.current.value, '"', '&quot;')

        // set html
        element.html('<input type="text" class="' + options.inputClasses + '" value="' + options.current.value + '" />')

        // store element
        options.current.element = $(element.find('input')[0])

        // set focus
        options.current.element.select()

        // bind events
        options.current.element.bind('blur', saveElement)
        options.current.element.keyup((e) => {
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
        const parent = options.current.element.parent()

        // get value and replace quotes, less than and greater than with their htmlentities
        let newValue = options.current.element.val()
        newValue = StringUtil.replaceAll(newValue, '"', '&quot;')
        newValue = StringUtil.replaceAll(newValue, '<', '&lt;')
        newValue = StringUtil.replaceAll(newValue, '>', '&gt;')

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
}
