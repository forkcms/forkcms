export class Step2 {
  constructor () {
    this.toggleLanguageType()
    this.handleLanguageChanges()
    this.setInterfaceDefaultLanguage()
  }

  /**
   * Toggles between multiple and single lexanguage site
   */
  toggleLanguageType () {
    if ($('#install_languages_language_type_1').is(':checked')) {
      $('#languages').show()
    }

    if ($('#install_languages_language_type_0').is(':checked')) {
      $('#language').show()
    }

    // multiple languages
    $('#install_languages_language_type_1').on('change', () => {
      if ($('#install_languages_language_type_1').is(':checked')) {
        $('#languages').show()
        $('#language').hide()
        $('#install_languages_default_language option').prop('disabled', true)
        $('#languages input:checked').each((index, element) => {
          $('#install_languages_default_language option[value=' + $(element).val() + ']').removeAttr('disabled')
        })
        if ($('#install_languages_default_language option[value=' + $('#install_languages_default_language').val() + ']').length === 0) $('#install_languages_default_language').val($('#install_languages_default_language option:enabled:first').val())
      }

      this.setInterfaceDefaultLanguage()
    })

    // single languages
    $('#install_languages_language_type_0').on('change', () => {
      if ($('#install_languages_language_type_0').is(':checked')) {
        $('#languages').hide()
        $('#language').show()
        $('#install_languages_default_language option').removeAttr('disabled')
      }

      this.setInterfaceDefaultLanguage()
    })
  }

  setInterfaceDefaultLanguage () {
    // same language as frontend
    if ($('#install_languages_same_interface_language').is(':checked')) {
      // just 1 language selected = only selected frontend language is available as interface language
      if ($('#install_languages_language_type_0').is(':checked')) {
        $('#install_languages_default_interface_language option').prop('disabled', true)
        $('#install_languages_default_interface_language option[value=' + $('#install_languages_default_language').val() + ']').removeAttr('disabled')
        $('#install_languages_default_interface_language').val($('#install_languages_default_interface_language option:enabled:first').val())
      } else if ($('#install_languages_language_type_1').is(':checked')) {
        $('#install_languages_default_interface_language option').prop('disabled', true)
        $('#languages input:checked').each((index, element) => {
          $('#install_languages_default_interface_language option[value=' + $(element).val() + ']').removeAttr('disabled')
        })

        if ($('#install_languages_default_interface_language option[value=' + $('#install_languages_default_interface_language').val() + ']').length === 0) {
          $('#install_languages_default_interface_language').val($('#install_languages_default_interface_language option:enabled:first').val())
        }
      }
    } else {
      // different languages than frontend
      $('#install_languages_default_interface_language option').prop('disabled', true)
      $('#interfaceLanguages input:checked').each((index, element) => {
        $('#install_languages_default_interface_language option[value=' + $(element).val() + ']').removeAttr('disabled')
      })
      if ($('#install_languages_default_interface_language option[value=' + $('#install_languages_default_interface_language').val() + ']').length === 0) {
        $('#install_languages_default_interface_language').val($('#install_languages_default_interface_language option:enabled:first').val())
      }
    }
  }

  handleLanguageChanges () {
    $('#languages input:checkbox').on('change', () => {
      $('#install_languages_default_language option').prop('disabled', true)
      $('#languages input:checked').each((index, element) => {
        $('#install_languages_default_language option[value=' + $(element).val() + ']').removeAttr('disabled')
      })
      if ($('#install_languages_default_language option[value=' + $('#install_languages_default_language').val() + ']').length === 0) {
        $('#install_languages_default_language').val($('#install_languages_default_language option:enabled:first').val())
      }

      this.setInterfaceDefaultLanguage()
    })

    $('#install_languages_default_language').on('change', () => {
      this.setInterfaceDefaultLanguage()
    })

    // interface language
    if ($('#install_languages_same_interface_language').is(':checked')) {
      $('#interfaceLanguagesExplanation').hide()
      $('#interfaceLanguages').hide()

      this.setInterfaceDefaultLanguage()
    }

    $('#install_languages_same_interface_language').on('change', () => {
      if ($('#install_languages_same_interface_language').is(':checked')) {
        $('#interfaceLanguagesExplanation').hide()
        $('#interfaceLanguages').hide()
      } else {
        $('#interfaceLanguagesExplanation').show()
        $('#interfaceLanguages').show()
      }

      this.setInterfaceDefaultLanguage()
    })

    $('#interfaceLanguages input:checkbox').on('change', () => {
      this.setInterfaceDefaultLanguage()
    })
  }
}
