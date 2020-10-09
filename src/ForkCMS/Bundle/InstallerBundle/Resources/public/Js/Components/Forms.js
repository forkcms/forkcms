export class Forms {
  constructor () {
    this.focusFirstField()
    this.submitWithLinks()
  }

  // set the focus on the first field
  focusFirstField () {
    $('form input:visible:not(.noFocus):first').focus()
  }

  // submit with links
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
          $('form#' + formId + '.submitWithLink input[type=submit]').each((index, el) => {
            $(el).after(replaceHTML.replace('{label}', $(el).val()).replace('{id}', $(el).attr('id')).replace('{class}', 'submitButton button ' + $(el).attr('class'))).css({
              position: 'absolute',
              top: '-9000px',
              left: '-9000px'
            }).attr('tabindex', -1)
          })

          // add onclick event for button (button can't have the name submit)
          $('form#' + formId + ' a.submitButton').on('click', (e) => {
            e.preventDefault()

            // is the button disabled?
            if ($(e.currentTarget).prop('disabled')) {
              return false
            } else {
              $('form#' + formId).submit()
            }
          })

          // don't submit the form on certain elements
          $('form#' + formId + ' .dontSubmit').on('focus', () => { dontSubmit = true })
          $('form#' + formId + ' .dontSubmit').on('blur', () => { dontSubmit = false })

          // hijack the submit event
          $('form#' + formId).submit((e) => { return !dontSubmit })
        }
      })
    }
  }
}
