export class StringUtil {
  /**
   * Camelcases a string
   *
   * @param string input
   * @param string splitchar
   * @return string
   */
  static camelCase (input, splitChar) {
    splitChar = typeof splitChar !== 'undefined' ? splitChar : '_'
    const regex = new RegExp(splitChar + '(.)', 'g')
    return input.toLowerCase().replace(regex, function (match, group1) {
      return group1.toUpperCase()
    })
  }

  /**
   * Fix a HTML5-chunk, so IE can render it
   *
   * @return string
   * @param string html
   */
  static html5 (html) {
    const html5 = 'abbr article aside audio canvas datalist details figcaption figure footer header hgroup mark meter nav output progress section summary time video'.split(' ')
    let i = 0
    let div = false

    // create div if needed
    if (div === false) {
      div = document.createElement('div')

      div.innerHTML = '<nav></nav>'

      if (div.childNodes.length !== 1) {
        const fragment = document.createDocumentFragment()
        i = html5.length
        while (i--) fragment.createElement(html5[i])

        fragment.appendChild(div)
      }
    }

    html = html.replace(/^\s\s*/, '').replace(/\s\s*$/, '')
      .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')

    // fix for when in a table
    const inTable = html.match(/^<(tbody|tr|td|th|col|colgroup|thead|tfoot)[\s/>]/i)

    if (inTable) {
      div.innerHTML = '<table>' + html + '</table>'
    } else {
      div.innerHTML = html
    }

    let scope
    if (inTable) {
      scope = div.getElementsByTagName(inTable[1])[0].parentNode
    } else {
      scope = div
    }

    const returnedFragment = document.createDocumentFragment()
    i = scope.childNodes.length
    while (i--) returnedFragment.appendChild(scope.firstChild)

    return returnedFragment
  }

  /**
   * Encode the string as HTML
   *
   * @return string
   * @param string value
   */
  static htmlEncode (value) {
    return $('<div/>').text(value).html()
  }

  /**
   * Decode the string as HTML
   *
   * @return string
   * @param string value
   */
  static htmlDecode (value) {
    return $('<div/>').html(value).text()
  }

  /**
   * Replace all occurences of one string into a string
   *
   * @return string
   * @param string value
   * @param string needle
   * @param string replacement
   */
  static replaceAll (value, needle, replacement) {
    if (typeof value === 'undefined') return ''
    return value.replace(new RegExp(needle, 'g'), replacement)
  }

  /**
   * Sprintf replaces all arguments that occur in the string (%1$s, %2$s, ...)
   *
   * @return string
   * @param string value
   * @params string arguments
   */
  static sprintf (value) {
    if (arguments.length < 2) {
      return value
    } else {
      // replace $ symbol first, because our RegExp won't except this symbol
      value = value.replace(/\$s/g, 'Ss')

      // find all variables and replace them
      for (let i = 1; i < arguments.length; i++) {
        value = StringUtil.replaceAll(value, '%' + i + 'Ss', arguments[i])
      }
    }

    return value
  }

  /**
   * Strip HTML tags
   *
   * @return string
   */
  static stripTags (value) {
    return value.replace(/<[^>]*>/ig, '')
  }

  /**
   * Strip whitespace from the beginning and end of a string
   *
   * @return string
   * @param string value
   * @param string charlist
   */
  static trim (value, charlist) {
    if (typeof value === 'undefined') return ''
    if (typeof charlist === 'undefined') charlist = ' '

    const pattern = new RegExp('^[' + charlist + ']*|[' + charlist + ']*$', 'g')
    return value.replace(pattern, '')
  }

  /**
   * Ucfirst a string
   *
   * @return string
   * @param string value
   */
  static ucfirst (value) {
    return value.charAt(0).toUpperCase() + value.slice(1)
  }

  /**
   * Tags cannot contain ", < and >
   *
   * @return string
   * @param string value
   */
  static stripForTag (value) {
    return value.replace(/"/g, '\'').replace(/(<|>)/g, '')
  }

  /**
   * Urlise a string (cfr. SpoonFilter::urlise)
   *
   * @return string
   * @param string value
   */
  static urlise (value) {
    // reserved characters (RFC 3986)
    const reservedCharacters = [
      '/', '?', ':', '@', '#', '[', ']',
      '!', '$', '&', '\'', '(', ')', '*',
      '+', ',', ';', '='
    ]

    // remove reserved characters
    for (let i in reservedCharacters) value = value.replace(reservedCharacters[i], ' ')

    // replace double quote, since this one might cause problems in html (e.g. <a href="double"quote">)
    value = StringUtil.replaceAll(value, '"', ' ')

    // replace spaces by dashes
    value = StringUtil.replaceAll(value, ' ', '-')

    // only urlencode if not yet urlencoded
    if (decodeURI(value) === value) {
      // to lowercase
      value = value.toLowerCase()

      // urlencode
      value = encodeURI(value)
    }

    // convert "--" to "-"
    value = value.replace(/-+/, '-')

    // trim - signs
    return StringUtil.trim(value, '-')
  }

  /**
   * Convert a HTML string to a XHTML string.
   *
   * @return string
   * @param string value
   */
  static xhtml (value) {
    // break tags should end with a slash
    value = value.replace(/<br>/g, '<br />')
    value = value.replace(/<br ?\/?>$/g, '')
    value = value.replace(/^<br ?\/?>/g, '')

    // image tags should end with a slash
    value = value.replace(/(<img [^>]+[^/])>/gi, '$1 />')

    // input tags should end with a slash
    value = value.replace(/(<input [^>]+[^/])>/gi, '$1 />')

    // big no-no to <b|i|u>
    value = value.replace(/<b\b[^>]*>(.*?)<\/b[^>]*>/g, '<strong>$1</strong>')
    value = value.replace(/<i\b[^>]*>(.*?)<\/i[^>]*>/g, '<em>$1</em>')
    value = value.replace(/<u\b[^>]*>(.*?)<\/u[^>]*>/g, '<span style="text-decoration:underline">$1</span>')

    // XHTML
    return value
  }
}
