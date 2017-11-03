export class Pagination {
  events () {
    $('[data-role="mobile-pagination"]').on('change', $.proxy(this.goToPage, this))
  }

  goToPage (event) {
    let $current = $(event.currentTarget)
    let nextPageId = $current.val()
    let url = window.location.href.split('?')[0]

    let parameters =  this.getParameters()

    parameters["page"] = nextPageId
    window.location.replace(url + '?' + decodeURIComponent($.param(parameters)))
  }

  getParameters () {
    if (typeof window.location.href.split('?')[1] === 'undefined') {
      return {}
    }

    let parameters = {}
    let queryString = window.location.href.split('?')[1]
    let rawParameters = queryString.split('&')

    $.each(rawParameters, (index, rawParameter) => {
      let parameterPair = rawParameter.split('=')
      parameters[parameterPair[0]] = parameterPair[1]
    })

    return parameters
  }
}
