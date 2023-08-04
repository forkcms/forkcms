export class Step2 {
  constructor() {
    this.toggleLanguageType()
    this.handleLanguageChanges()
    this.setUserDefaultLanguage()
  }

  /**
   * Toggles between multiple and single lexanguage site
   */
  toggleLanguageType() {
    $('[data-fork-cms-role="multilingual"]').on('change', (event) => {
      if ($(event.target).is(':checked')) {
        $('[data-fork-cms-role="multilingual-wrapper"]').show()
        $('[data-fork-cms-role="default-locale"] option').prop('disabled', true)
        $('[data-fork-cms-role="locales"] input:checked').each((index, element) => {
          $('[data-fork-cms-role="default-locale"] option[value=' + $(element).val() + ']').removeAttr('disabled')
        })
        let $defaultLocaleSelector = $('[data-fork-cms-role="default-locale"]');
        if ($('[data-fork-cms-role="default-locale"] option[value=' + $defaultLocaleSelector.val() + ']').length === 0) {
          $defaultLocaleSelector.val($('[data-fork-cms-role="default-locale"] option:enabled:first').val())
        }
      }

      this.setUserDefaultLanguage()
    }).trigger('change')

    // single languages
    $('[data-fork-cms-role="not-multilingual"]').on('change', (event) => {
      if ($(event.target).is(':checked')) {
        $('[data-fork-cms-role="multilingual-wrapper"]').hide()
        $('[data-fork-cms-role="default-locale"] option').removeAttr('disabled')
      }

      this.setUserDefaultLanguage()
    }).trigger('change')
  }

  setUserDefaultLanguage() {
    let $defaultUserLocale = $('[data-fork-cms-role="default-user-locale"]')
    let $defaultUserLocaleOptions = $defaultUserLocale.find('option')
    // same language as frontend
    if ($('[data-fork-cms-role="same-user-locale"]').is(':checked')) {
      // just 1 language selected = only selected frontend language is available as user language
      if ($('[data-fork-cms-role="not-multilingual"]').is(':checked')) {
        $defaultUserLocaleOptions.prop('disabled', true)
        $('[data-fork-cms-role="default-user-locale"] option[value=' + $('[data-fork-cms-role="default-locale"]').val() + ']').removeAttr('disabled')
        $defaultUserLocale.val($('[data-fork-cms-role="default-user-locale"] option:enabled:first').val())
      }
      else if ($('[data-fork-cms-role="multilingual"]').is(':checked')) {
        $defaultUserLocaleOptions.prop('disabled', true)
        $('[data-fork-cms-role="locales"] input:checked').each((index, element) => {
          $('[data-fork-cms-role="default-user-locale"] option[value=' + $(element).val() + ']').removeAttr('disabled')
        })

        if ($('[data-fork-cms-role="default-user-locale"] option[value=' + $defaultUserLocale.val() + ']').length === 0) {
          $defaultUserLocale.val($('[data-fork-cms-role="default-user-locale"] option:enabled:first').val())
        }
      }
    }
    else {
      // different languages than frontend
      $defaultUserLocaleOptions.prop('disabled', true)
      $('[data-fork-cms-role="user-locales"] input:checked').each((index, element) => {
        $('[data-fork-cms-role="default-user-locale"] option[value=' + $(element).val() + ']').removeAttr('disabled')
      })
      if ($('[data-fork-cms-role="default-user-locale"] option[value=' + $defaultUserLocale.val() + ']').length === 0) {
        $defaultUserLocale.val($('[data-fork-cms-role="default-user-locale"] option:enabled:first').val())
      }
    }
  }

  handleLanguageChanges() {
    $('[data-fork-cms-role="locales"] input:checkbox').on('change', () => {
      $('[data-fork-cms-role="default-locale"] option').prop('disabled', true)
      $('[data-fork-cms-role="locales"] input:checked').each((index, element) => {
        $('[data-fork-cms-role="default-locale"] option[value=' + $(element).val() + ']').removeAttr('disabled')
      })
      let $defaultLocale = $('[data-fork-cms-role="default-locale"]')
      if ($('[data-fork-cms-role="default-locale"] option[value=' + $defaultLocale.val() + ']').length === 0) {
        $defaultLocale.val($('[data-fork-cms-role="default-locale"] option:enabled:first').val())
      }

      this.setUserDefaultLanguage()
    })

    $('[data-fork-cms-role="default-locale"]').on('change', () => {
      this.setUserDefaultLanguage()
    })

    // user language
    $('[data-fork-cms-role="same-user-locale"]').on('change', () => {
      if ($('[data-fork-cms-role="same-user-locale"]').is(':checked')) {
        $('[data-fork-cms-role="user-locales"]').hide()
      }
      else {
        $('[data-fork-cms-role="user-locales"]').show()
      }

      this.setUserDefaultLanguage()
    }).trigger('change')

    $('[data-fork-cms-role="user-locales"] input:checkbox').on('change', () => {
      this.setUserDefaultLanguage()
    })
  }
}
