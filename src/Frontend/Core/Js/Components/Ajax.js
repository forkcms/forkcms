import { Config } from './Config'

export class Ajax {
  constructor () {
    // set defaults for AJAX
    $.ajaxSetup({
      url: '/frontend/ajax',
      cache: false,
      type: 'POST',
      dataType: 'json',
      timeout: 10000,
      data: {
        fork: {
          module: null,
          action: null,
          language: Config.getCurrentLanguage()
        }
      }
    })
  }
}
