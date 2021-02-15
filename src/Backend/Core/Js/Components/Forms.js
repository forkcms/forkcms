import Bloodhound from 'typeahead.js/dist/bloodhound'
import { Meta } from './Meta'

export class Forms {
  constructor () {
    this.placeholders() // make sure this is done before focusing the first field
    this.focusFirstField()
    this.datefields()
    this.submitWithLinks()
    this.tagsInput()
    this.meta()
    this.datePicker()
    this.bootstrapTabFormValidation()
    this.imagePreview()
    this.fileUpload()
    this.select2()
  }

  fileUpload () {
    $('.form-control[type="file"]').on('change', (event) => {
      let file = ''
      event = event.originalEvent

      for (let i = 0; i < event.target.files.length; i++) {
        file = event.target.files[i]
      }

      $(event.currentTarget).siblings('.form-label').text(file.name)
    })
  }

  select2 () {
    $.fn.select2.defaults.set('theme', 'bootstrap')

    $.fn.select2.defaults.set('language', {
      noResults: () => {
        return window.backend.locale.lbl('NoResultsFound')
      },
      errorLoading: () => {
        return window.backend.local.lbl('TheResultsCouldNotBeLoaded')
      },
      loadingMore: () => {
        return window.backend.locale.lbl('LoadingMoreResults')
      },
      searching: () => {
        return window.backend.locale.lbl('Searching')
      }
    })

    $('[data-fork="select2"]').select2()
  }

  imagePreview () {
    $('input[type=file]').on('change', (event) => {
      const imageField = event.target
      // make sure we are uploading an image by checking the data attribute
      if (imageField.getAttribute('data-fork-cms-role') === 'image-field' && imageField.files && imageField.files[0]) {
        // get the image preview by matching the image-preview data-id to the ImageField id
        const $imagePreview = $('[data-fork-cms-role="image-preview"][data-id="' + imageField.id + '"]')
        // use FileReader to get the url
        const reader = new window.FileReader()

        reader.onload = function (event) {
          $imagePreview.attr('src', event.target.result)
        }

        reader.readAsDataURL(imageField.files[0])
      }
    })
  }

  bootstrapTabFormValidation () {
    $('.tab-pane input, .tab-pane textarea, .tab-pane select').on('invalid', (event) => {
      const $invalidField = $(event.currentTarget)
      // Find the tab-pane that this element is inside, and get the id
      const invalidTabId = $invalidField.closest('.tab-pane').attr('id')

      // Find the link that corresponds to the pane and have it show
      $('a[href=#' + invalidTabId + '], [data-bs-target=#' + invalidTabId + ']').tab('show')
      $invalidField.focus()
    })
  }

  meta () {
    const $metaTabs = $('.js-do-meta-automatically')
    if ($metaTabs.length === 0) {
      return
    }

    $metaTabs.each((index, element) => {
      Meta.doMeta(element.dataset, element.dataset.baseFieldSelector)
    })
  }

  datefields () {
    // variables
    const dayNames = [
      window.backend.locale.loc('DayLongSun'), window.backend.locale.loc('DayLongMon'), window.backend.locale.loc('DayLongTue'),
      window.backend.locale.loc('DayLongWed'), window.backend.locale.loc('DayLongThu'), window.backend.locale.loc('DayLongFri'),
      window.backend.locale.loc('DayLongSat')
    ]
    const dayNamesMin = [
      window.backend.locale.loc('DayShortSun'), window.backend.locale.loc('DayShortMon'), window.backend.locale.loc('DayShortTue'),
      window.backend.locale.loc('DayShortWed'), window.backend.locale.loc('DayShortThu'), window.backend.locale.loc('DayShortFri'),
      window.backend.locale.loc('DayShortSat')
    ]
    const dayNamesShort = [
      window.backend.locale.loc('DayShortSun'), window.backend.locale.loc('DayShortMon'), window.backend.locale.loc('DayShortTue'),
      window.backend.locale.loc('DayShortWed'), window.backend.locale.loc('DayShortThu'), window.backend.locale.loc('DayShortFri'),
      window.backend.locale.loc('DayShortSat')
    ]
    const monthNames = [
      window.backend.locale.loc('MonthLong1'), window.backend.locale.loc('MonthLong2'), window.backend.locale.loc('MonthLong3'),
      window.backend.locale.loc('MonthLong4'), window.backend.locale.loc('MonthLong5'), window.backend.locale.loc('MonthLong6'),
      window.backend.locale.loc('MonthLong7'), window.backend.locale.loc('MonthLong8'), window.backend.locale.loc('MonthLong9'),
      window.backend.locale.loc('MonthLong10'), window.backend.locale.loc('MonthLong11'), window.backend.locale.loc('MonthLong12')
    ]
    const monthNamesShort = [
      window.backend.locale.loc('MonthShort1'), window.backend.locale.loc('MonthShort2'), window.backend.locale.loc('MonthShort3'),
      window.backend.locale.loc('MonthShort4'), window.backend.locale.loc('MonthShort5'), window.backend.locale.loc('MonthShort6'),
      window.backend.locale.loc('MonthShort7'), window.backend.locale.loc('MonthShort8'), window.backend.locale.loc('MonthShort9'),
      window.backend.locale.loc('MonthShort10'), window.backend.locale.loc('MonthShort11'), window.backend.locale.loc('MonthShort12')
    ]
    const $inputDatefieldNormal = $('.inputDatefieldNormal')
    const $inputDatefieldFrom = $('.inputDatefieldFrom')
    const $inputDatefieldTill = $('.inputDatefieldTill')
    const $inputDatefieldRange = $('.inputDatefieldRange')

    $('.inputDatefieldNormal, .inputDatefieldFrom, .inputDatefieldTill, .inputDatefieldRange').datepicker(
      {
        dayNames: dayNames,
        dayNamesMin: dayNamesMin,
        dayNamesShort: dayNamesShort,
        hideIfNoPrevNext: true,
        monthNames: monthNames,
        monthNamesShort: monthNamesShort,
        nextText: window.backend.locale.lbl('Next'),
        prevText: window.backend.locale.lbl('Previous'),
        showAnim: 'slideDown'
      })

    // the default, nothing special
    $inputDatefieldNormal.each((index, element) => {
      // variables
      const $this = $(element)

      // get data
      const data = $(element).data()
      const value = $(element).val()

      // set options
      $this.datepicker('option',
        {
          dateFormat: data.mask,
          firstDate: data.firstday
        }).datepicker('setDate', value)
    })

    // date fields that have a certain start date
    $inputDatefieldFrom.each((index, element) => {
      // variables
      const $this = $(element)

      // get data
      const data = $(element).data()
      const value = $(element).val()

      // set options
      $this.datepicker('option',
        {
          dateFormat: data.mask,
          firstDay: data.firstday,
          minDate: new Date(parseInt(data.startdate.split('-')[0], 10), parseInt(data.startdate.split('-')[1], 10) - 1, parseInt(data.startdate.split('-')[2], 10))
        }).datepicker('setDate', value)
    })

    // date fields that have a certain end date
    $inputDatefieldTill.each((index, element) => {
      // variables
      const $this = $(element)

      // get data
      const data = $(element).data()
      const value = $(element).val()

      // set options
      $this.datepicker('option',
        {
          dateFormat: data.mask,
          firstDay: data.firstday,
          maxDate: new Date(parseInt(data.enddate.split('-')[0], 10), parseInt(data.enddate.split('-')[1], 10) - 1, parseInt(data.enddate.split('-')[2], 10))
        }).datepicker('setDate', value)
    })

    // date fields that have a certain range
    $inputDatefieldRange.each((index, element) => {
      // variables
      const $this = $(element)

      // get data
      const data = $(element).data()
      const value = $(element).val()

      // set options
      $this.datepicker('option',
        {
          dateFormat: data.mask,
          firstDay: data.firstday,
          minDate: new Date(parseInt(data.startdate.split('-')[0], 10), parseInt(data.startdate.split('-')[1], 10) - 1, parseInt(data.startdate.split('-')[2], 10), 0, 0, 0, 0),
          maxDate: new Date(parseInt(data.enddate.split('-')[0], 10), parseInt(data.enddate.split('-')[1], 10) - 1, parseInt(data.enddate.split('-')[2], 10), 23, 59, 59)
        }).datepicker('setDate', value)
    })
  }

  // set the focus on the first field
  focusFirstField () {
    $('form input:visible:not(.noFocus):first').focus()
  }

  // set placeholders
  placeholders () {
    // detect if placeholder-attribute is supported
    jQuery.support.placeholder = ('placeholder' in document.createElement('input'))

    if (!jQuery.support.placeholder) {
      // variables
      const $placeholder = $('input[placeholder]')

      // bind focus
      $placeholder.on('focus', (event) => {
        // grab element
        const $input = $(event.currentTarget)

        // only do something when the current value and the placeholder are the same
        if ($input.val() === $input.attr('placeholder')) {
          // clear
          $input.val('')

          // remove class
          $input.removeClass('placeholder')
        }
      })

      $placeholder.blur((event) => {
        // grab element
        const $input = $(event.currentTarget)

        // only do something when the input is empty or the value is the same as the placeholder
        if ($input.val() === '' || $input.val() === $input.attr('placeholder')) {
          // set placeholder
          $input.val($input.attr('placeholder'))

          // add class
          $input.addClass('placeholder')
        }
      })

      // call blur to initialize
      $placeholder.blur()

      // hijack the form so placeholders aren't submitted as values
      $placeholder.parents('form').submit((event) => {
        // find elements with placeholders
        $(event.currentTarget).find('input[placeholder]').each((index, element) => {
          // grab element
          const $input = $(element)

          // if the value and the placeholder are the same reset the value
          if ($input.val() === $input.attr('placeholder')) $input.val('')
        })
      })
    }
  }

  // replaces buttons with <a><span>'s (to allow more flexible styling) and handle the form submission for them
  submitWithLinks () {
    // the html for the button that will replace the input[submit]
    const replaceHTML = '<a class="{class}" href="#{id}"><span>{label}</span></a>'

    // are there any forms that should be submitted with a link?
    if ($('form.submitWithLink').length > 0) {
      $('form.submitWithLink').each((index, element) => {
        // get id
        const formId = $(element).attr('id')
        let dontSubmit = false

        // validate id
        if (formId !== '') {
          // loop every button to be replaced
          $('form#' + formId + '.submitWithLink input[type=submit]').each((index, element) => {
            $(element).after(replaceHTML.replace('{label}', $(element).val()).replace('{id}', $(element).attr('id')).replace('{class}', 'submitButton button ' + $(element).attr('class'))).css({
              position: 'absolute',
              top: '-9000px',
              left: '-9000px'
            }).attr('tabindex', -1)
          })

          // add onclick event for button (button can't have the name submit)
          $('form#' + formId + ' a.submitButton').on('click', (event) => {
            event.preventDefault()

            // is the button disabled?
            if ($(event.currentTarget).prop('disabled')) {
              return false
            } else {
              $('form#' + formId).submit()
            }
          })

          // dont submit the form on certain elements
          $('form#' + formId + ' .dontSubmit').on('focus', () => {
            dontSubmit = true
          })
          $('form#' + formId + ' .dontSubmit').on('blur', () => {
            dontSubmit = false
          })

          // hijack the submit event
          $('form#' + formId).submit(() => {
            return !dontSubmit
          })
        }
      })
    }
  }

  // add tagsinput to the correct input fields
  tagsInput () {
    if ($('.js-tags-input').length > 0) {
      const allTags = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        prefetch: {
          url: '/backend/ajax',
          prepare: (settings) => {
            settings.type = 'POST'
            settings.data = {fork: {module: 'Tags', action: 'GetAllTags'}}
            return settings
          },
          cache: false,
          filter: (list) => {
            list = list.data
            return list
          }
        }
      })

      allTags.initialize()
      $('.js-tags-input').tagsinput({
        tagClass: 'badge badge-primary',
        typeaheadjs: {
          name: 'Tags',
          source: allTags.ttAdapter()
        }
      })
    }
  }

  // show a warning when people are leaving the
  unloadWarning () {
    // only execute when there is a form on the page
    if ($('form:visible').length > 0) {
      // loop fields
      $('form input, form select, form textarea').each((index, element) => {
        const $this = $(element)

        if (!$this.hasClass('dontCheckBeforeUnload')) {
          // store initial value
          $this.data('initial-value', $this.val()).addClass('checkBeforeUnload')
        }
      })

      // bind before unload, this will ask the user if he really wants to leave the page
      $(window).on('beforeunload', this.unloadWarningCheck)

      // if a form is submitted we don't want to ask the user if he wants to leave, we know for sure
      $('form').on('submit', (event) => {
        if (!event.isDefaultPrevented()) $(window).off('beforeunload')
      })
    }
  }

  // check if any element has been changed
  unloadWarningCheck () {
    // initialize var
    let changed = false

    // loop fields
    $('.checkBeforeUnload').each((index, element) => {
      // initialize
      const $this = $(element)

      // compare values
      if ($this.data('initial-value') !== $this.val()) {
        if (typeof $this.data('initial-value') === 'undefined' && $this.val() === '') {
        } else {
          // reset var
          changed = true

          // stop looking
          return false
        }
      }
    })

    // return if needed
    if (changed) return window.backend.locale.msg('ValuesAreChanged')
  }

  // Add date pickers to the appropriate input elements
  datePicker () {
    $('input[data-role="fork-datepicker"]').each((index, datePickerElement) => {
      $(datePickerElement).datepicker()
    })
  }
}
