/**
 * Functions related to the current url
 */

export class UrlUtil {
  static extractParamFromUri (uri, paramName) {
    if (!uri) return
    uri = uri.split('#')[0]
    const parts = uri.split('?')
    if (parts.length === 1) return

    const query = decodeURI(parts[1])

    paramName += '='
    const params = query.split('&')
    for (let i = 0; i < params.length; ++i) {
      const param = params[i]
      if (param.indexOf(paramName) === 0) return decodeURIComponent(param.split('=')[1])
    }
  }

  /**
   * Get a GET parameter
   *
   * @return string
   * @param string name
   */
  static getGetValue (name) {
    // init return value
    let getValue = ''

    // get GET chunks from url
    const hashes = window.location.search.slice(window.location.search.indexOf('?') + 1).split('&')

    // find requested parameter
    $.each(hashes, (index, value) => {
      // split name/value up
      const chunks = value.split('=')

      // found the requested parameter
      if (chunks[0] === name) {
        // set for return
        getValue = chunks[1]

        // break loop
        return false
      }
    })

    // cough up value
    return getValue
  }
}
