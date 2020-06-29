export class Data {
  constructor () {
    this.initialized = false
    this.data = {}
  }

  init () {
    // check if var is available
    if (typeof jsData === 'undefined') throw new Error('jsData is not available')

    // populate
    this.data = jsData
    this.initialized = true
  }

  exists (key) {
    return (typeof this.get(key) !== 'undefined')
  }

  get (key) {
    // init if needed
    if (!this.initialized) this.init()

    var keys = key.split('.')
    var data = this.data
    for (var i = 0; i < keys.length; i++) {
      data = data[keys[i]]
    }

    // return
    return data
  }
}
