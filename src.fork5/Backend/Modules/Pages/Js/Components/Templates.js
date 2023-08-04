export class Templates {
  constructor () {
    $('[data-role="template-switcher"]').on('change', (e) => {
      window.location.replace(this.getChangeTemplateUrl('template-id', $(e.currentTarget).val()))
    })
  }

  getChangeTemplateUrl (key, value, url) {
    if (!url) url = this.getChangeTemplateUrl('report', null, window.location.href)
    const re = new RegExp('([?&])' + key + '=.*?(&|#|$)(.*)', 'gi')
    let hash

    if (re.test(url)) {
      if (typeof value !== 'undefined' && value !== null) {
        return url.replace(re, '$1' + key + '=' + value + '$2$3')
      }

      hash = url.split('#')
      url = hash[0].replace(re, '$1$3').replace(/(&|\?)$/, '')
      if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
        url += '#' + hash[1]
      }
      return url
    }
    if (typeof value !== 'undefined' && value !== null) {
      const separator = url.indexOf('?') !== -1 ? '&' : '?'
      hash = url.split('#')
      url = hash[0] + separator + key + '=' + value
      if (typeof hash[1] !== 'undefined' && hash[1] !== null) {
        url += '#' + hash[1]
      }
      return url
    }

    return url
  }
}
