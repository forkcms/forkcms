import Translator from 'bazinga-translator'

export class Locale {
  constructor (locale, defaultTranslationDomain, defaultFallbackDomain) {
    this.locale = locale
    this.defaultTranslationDomain = defaultTranslationDomain
    this.defaultFallbackDomain = defaultFallbackDomain

    this.init()
  }

  init () {
    if (Locale.loadedLocale === this.locale) {
      return
    }

    $.ajax({
      url: '/_translations/' + this.locale + '.json',
      type: 'GET',
      dataType: 'json',
      async: false,
      success: (translations) => {
        Translator.fromJSON(translations)
        Locale.loadedLocale = this.locale
      },
      error: (jqXHR, textStatus, errorThrown) => {
        throw new Error('Regenerate your locale-files.')
      }
    })
  }

  // get an item from the locale
  get (type, key, domain, parameters) {
    return this.trans(type + '.' + key, parameters, domain)
  }

  trans (id, parameters, domain, locale) {
    let translation = Translator.trans(id, parameters, domain || this.defaultTranslationDomain, locale)

    if (translation !== id) {
      return translation
    }

    translation = Translator.trans(id, parameters, this.defaultFallbackDomain, locale)

    if (translation !== id) {
      return translation
    }

    console.debug('Missing translation: ' + id)

    return id
  }

  transChoice (id, number, parameters, domain, locale) {
    let translation = Translator.transChoice(id, number, parameters, domain || this.defaultTranslationDomain, locale)

    if (translation !== id) {
      return translation
    }

    translation = Translator.transChoice(id, number, parameters, this.defaultFallbackDomain, locale)

    if (translation !== id) {
      return translation
    }

    console.debug('Missing translation: ' + id)

    return id
  }

  // get an error
  err (key, module, parameters) {
    return this.get('err', key, module || this.defaultTranslationDomain, parameters)
  }

  // get a label
  lbl (key, module, parameters) {
    return this.get('lbl', key, module || this.defaultTranslationDomain, parameters)
  }

  // get localization
  loc (key) {
    return this.get('loc', key)
  }

  // get a message
  msg (key, module, parameters) {
    return this.get('msg', key, module || this.defaultTranslationDomain, parameters)
  }

  // get a slug
  slg (key, module, parameters) {
    return this.get('slg', key, module || this.defaultTranslationDomain, parameters)
  }
}

Locale.loadedLocale = false
