export class Pagination {
  events () {
    $('[data-role="mobile-pagination"]').on('change', $.proxy(this.goToPage, this))
  }

  goToPage (event) {
    let $current = $(event.currentTarget)
    let nextPageId = $current.val()

    this.changePageInUrlAndReload(nextPageId)
  }

  changePageInUrlAndReload (value) {
    const key = 'page'
    let parameters = document.location.search.substr(1).split('&')
    let i = parameters.length

    value = encodeURI(value)

    while (i--) {
      let parameter = parameters[i].split('=')

      if (parameter[0] === key) {
        parameter[1] = value
        parameters[i] = parameter.join('=')
        break
      }
    }

    if (i < 0) {
      parameters[parameters.length] = [key, value].join('=')
    }

    document.location.search = parameters.join('&')
  }
}
