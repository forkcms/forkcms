export class Data {
  static exists (key) {
    return (typeof Data.get(key) !== 'undefined')
  }

  static get (key) {
    // init if needed
    if (typeof jsData === 'undefined') throw new Error('jsData is not available')

    const keys = key.split('.')
    let data = jsData
    for (let i = 0; i < keys.length; i++) {
      data = data[keys[i]]
    }

    // return
    return data
  }
}
