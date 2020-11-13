import { Html5Validation } from './Html5Validation'

export class Forms {
  constructor () {
    this.placeholders()
    this.datefields()
    this.validation()
    this.filled()
    this.datePicker()
    this.imagePreview()
    this.requiredTooltip()
  }

  requiredTooltip () {
    $(document).on('focus', '.form-control', (event) => {
      const id = $(event.currentTarget).attr('id')

      // show tooltip
      $('label[for="' + id + '"]').find('abbr').tooltip('show')

      // hide tooltip after 1 second
      setTimeout(() => {
        $('label[for="' + id + '"]').find('abbr').tooltip('hide')
      }, 1000)
    })
  }

  imagePreview () {
    $('input[type=file]').on('change', () => {
      const imageField = $(this).get(0)

      // make sure we are uploading an image by checking the data attribute
      if (imageField.getAttribute('data-fork-cms-role') === 'image-field' && imageField.files && imageField.files[0]) {
        // get the image preview by matching the image-preview data-id to the ImageField id
        let $imagePreviewWrapper = $('[data-fork-cms-role="image-preview-wrapper"][data-id="' + imageField.id + '"]')

        // use FileReader to get the url
        const reader = new window.FileReader()

        reader.onload = (event) => {
          if ($imagePreviewWrapper.find('img').length > 0) {
            $imagePreviewWrapper.find('img').attr('src', event.target.result)
          } else {
            $imagePreviewWrapper.append('<img src="' + event.target.result + '" class="img-thumbnail" />')
          }
        }

        reader.readAsDataURL(imageField.files[0])
      }
    })
  }

  // once text has been filled add another class to it (so it's possible to style it differently)
  filled () {
    $(document).on('blur', 'form input, form textarea, form select', (e) => {
      if ($(e.currentTarget).val() === '') {
        $(e.currentTarget).removeClass('filled')
      } else {
        $(e.currentTarget).addClass('filled')
      }
    })
  }

  // initialize the date fields
  datefields () {
    // jQuery datapicker fallback for browsers that don't support the HTML5 date type
    const $inputDateType = $('input.inputDatefield')
    if ($inputDateType.length) {
      // the browser does not support the HTML5 data type
      if ($inputDateType.get(0).type !== 'date') {
        $inputDateType.addClass('inputDatefieldNormal')
      }
    }

    const $inputDatefields = $('.inputDatefieldNormal, .inputDatefieldFrom, .inputDatefieldTill, .inputDatefieldRange')
    const $inputDatefieldNormal = $('.inputDatefieldNormal')
    const $inputDatefieldFrom = $('.inputDatefieldFrom')
    const $inputDatefieldTill = $('.inputDatefieldTill')
    const $inputDatefieldRange = $('.inputDatefieldRange')

    if ($inputDatefields.length > 0) {
      const dayNames = [
        window.frontend.components.locale.loc('DayLongSun'), window.frontend.components.locale.loc('DayLongMon'), window.frontend.components.locale.loc('DayLongTue'),
        window.frontend.components.locale.loc('DayLongWed'), window.frontend.components.locale.loc('DayLongThu'), window.frontend.components.locale.loc('DayLongFri'),
        window.frontend.components.locale.loc('DayLongSat')
      ]
      const dayNamesMin = [
        window.frontend.components.locale.loc('DayShortSun'), window.frontend.components.locale.loc('DayShortMon'),
        window.frontend.components.locale.loc('DayShortTue'), window.frontend.components.locale.loc('DayShortWed'),
        window.frontend.components.locale.loc('DayShortThu'), window.frontend.components.locale.loc('DayShortFri'),
        window.frontend.components.locale.loc('DayShortSat')
      ]
      const dayNamesShort = [
        window.frontend.components.locale.loc('DayShortSun'), window.frontend.components.locale.loc('DayShortMon'),
        window.frontend.components.locale.loc('DayShortTue'), window.frontend.components.locale.loc('DayShortWed'),
        window.frontend.components.locale.loc('DayShortThu'), window.frontend.components.locale.loc('DayShortFri'),
        window.frontend.components.locale.loc('DayShortSat')
      ]
      const monthNames = [
        window.frontend.components.locale.loc('MonthLong1'), window.frontend.components.locale.loc('MonthLong2'), window.frontend.components.locale.loc('MonthLong3'),
        window.frontend.components.locale.loc('MonthLong4'), window.frontend.components.locale.loc('MonthLong5'), window.frontend.components.locale.loc('MonthLong6'),
        window.frontend.components.locale.loc('MonthLong7'), window.frontend.components.locale.loc('MonthLong8'), window.frontend.components.locale.loc('MonthLong9'),
        window.frontend.components.locale.loc('MonthLong10'), window.frontend.components.locale.loc('MonthLong11'),
        window.frontend.components.locale.loc('MonthLong12')
      ]
      const monthNamesShort = [
        window.frontend.components.locale.loc('MonthShort1'), window.frontend.components.locale.loc('MonthShort2'),
        window.frontend.components.locale.loc('MonthShort3'), window.frontend.components.locale.loc('MonthShort4'),
        window.frontend.components.locale.loc('MonthShort5'), window.frontend.components.locale.loc('MonthShort6'),
        window.frontend.components.locale.loc('MonthShort7'), window.frontend.components.locale.loc('MonthShort8'),
        window.frontend.components.locale.loc('MonthShort9'), window.frontend.components.locale.loc('MonthShort10'),
        window.frontend.components.locale.loc('MonthShort11'), window.frontend.components.locale.loc('MonthShort12')
      ]

      if ($.isFunction($.fn.datepicker)) {
        $inputDatefieldNormal.each((index, element) => {
          // Create a hidden clone (before datepicker init!), which will contain the actual value
          const clone = $(element).clone()
          clone.insertAfter(element)
          clone.hide()

          // Rename the original field, used to contain the display value
          $(element).attr('id', $(element).attr('id') + '-display')
          $(element).attr('name', $(element).attr('name') + '-display')

          // make sure we can make the value empty
          $(element).on('change', (event) => {
            if ($(event.currentTarget).val() === '') {
              clone.val('')
            }
          })
        })

        $inputDatefields.datepicker({
          dayNames: dayNames,
          dayNamesMin: dayNamesMin,
          dayNamesShort: dayNamesShort,
          hideIfNoPrevNext: true,
          monthNames: monthNames,
          monthNamesShort: monthNamesShort,
          nextText: window.frontend.components.locale.lbl('Next'),
          prevText: window.frontend.components.locale.lbl('Previous'),
          showAnim: 'slideDown'
        })

        // the default, nothing special
        $inputDatefieldNormal.each((index, element) => {
          // get data
          const data = $(element).data()
          const phpDate = new Date(data.year, data.month, data.day, 0, 0, 0) // Get date from php in YYYY-MM-DD format
          const value = ($(element).val() !== '') ? $.datepicker.formatDate(data.mask, phpDate) : '' // Convert the value to the data-mask to display it

          // Create the datepicker with the desired display format and alt field
          $(element).datepicker('option', {
            dateFormat: data.mask,
            firstDay: data.firstday,
            altField: '#' + $(element).attr('id').replace('-display', ''),
            altFormat: 'yy-mm-dd'
          }).datepicker('setDate', value)
        })

        // date fields that have a certain start date
        $inputDatefieldFrom.each((index, element) => {
          // get data
          const data = $(element).data()
          const value = $(element).val()

          // set options
          $(element).datepicker('option', {
            dateFormat: data.mask,
            firstDay: data.firstday,
            minDate: new Date(parseInt(data.startdate.split('-')[0], 10), parseInt(data.startdate.split('-')[1], 10) - 1, parseInt(data.startdate.split('-')[2], 10))
          }).datepicker('setDate', value)
        })

        // date fields that have a certain enddate
        $inputDatefieldTill.each((index, element) => {
          // get data
          const data = $(element).data()
          const value = $(element).val()

          // set options
          $(element).datepicker('option',
            {
              dateFormat: data.mask,
              firstDay: data.firstday,
              maxDate: new Date(parseInt(data.enddate.split('-')[0], 10), parseInt(data.enddate.split('-')[1], 10) - 1, parseInt(data.enddate.split('-')[2], 10))
            }).datepicker('setDate', value)
        })

        // date fields that have a certain range
        $inputDatefieldRange.each((index, element) => {
          // get data
          const data = $(element).data()
          const value = $(element).val()

          // set options
          $(element).datepicker('option',
            {
              dateFormat: data.mask,
              firstDay: data.firstday,
              minDate: new Date(parseInt(data.startdate.split('-')[0], 10), parseInt(data.startdate.split('-')[1], 10) - 1, parseInt(data.startdate.split('-')[2], 10), 0, 0, 0, 0),
              maxDate: new Date(parseInt(data.enddate.split('-')[0], 10), parseInt(data.enddate.split('-')[1], 10) - 1, parseInt(data.enddate.split('-')[2], 10), 23, 59, 59)
            }).datepicker('setDate', value)
        })
      }
    }
  }

  validation () {
    $('input, textarea, select').each((index, element) => {
      const $input = $(element)
      const options = {}

      // Check for custom error messages
      $.each($input.data(), (key, value) => {
        if (key.indexOf('error') < 0) return
        key = key.replace('error', '').toLowerCase()
        options[key] = value
      })

      Html5Validation.html5validation(options, $input)
    })
  }

  // placeholder fallback for browsers that don't support placeholder
  placeholders () {
    // detect if placeholder-attribute is supported
    jQuery.support.placeholder = ('placeholder' in document.createElement('input'))

    if (!jQuery.support.placeholder) {
      // bind focus
      $('input[placeholder], textarea[placeholder]').on('focus', (e) => {
        // grab element
        const input = $(e.currentTarget)

        // only do something when the current value and the placeholder are the same
        if (input.val() === input.attr('placeholder')) {
          // clear
          input.val('')

          // remove class
          input.removeClass('placeholder')
        }
      })

      $('input[placeholder], textarea[placeholder]').on('blur', (e) => {
        // grab element
        const input = $(e.currentTarget)

        // only do something when the input is empty or the value is the same as the placeholder
        if (input.val() === '' || input.val() === input.attr('placeholder')) {
          // set placeholder
          input.val(input.attr('placeholder'))

          // add class
          input.addClass('placeholder')
        }
      })

      // call blur to initialize
      $('input[placeholder], textarea[placeholder]').blur()

      // hijack the form so placeholders aren't submitted as values
      $('input[placeholder], textarea[placeholder]').parents('form').submit((e) => {
        // find elements with placeholders
        $(e.currentTarget).find('input[placeholder]').each((index, element) => {
          // grab element
          const input = $(element)

          // if the value and the placeholder are the same reset the value
          if (input.val() === input.attr('placeholder')) input.val('')
        })
      })
    }
  }

  // Add date pickers to the appropriate input elements
  datePicker () {
    $('input[data-role="fork-datepicker"]').each((index, datePickerElement) => {
      $(datePickerElement).datepicker()
    })
  }
}
