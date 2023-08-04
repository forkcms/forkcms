export class Facebook {
  // will be called after Facebook is initialized
  afterInit () {
    // is GA available?
    if (typeof _gaq === 'object') {
      // subscribe and track like
      FB.Event.subscribe('edge.create', (targetUrl) => {
        _gaq.push([
          '_trackSocial', 'facebook', 'like', targetUrl
        ])
      })

      // subscribe and track unlike
      FB.Event.subscribe('edge.remove', (targetUrl) => {
        _gaq.push([
          '_trackSocial', 'facebook', 'unlike', targetUrl
        ])
      })

      // subscribe and track message
      FB.Event.subscribe('message.send', (targetUrl) => {
        _gaq.push([
          '_trackSocial', 'facebook', 'send', targetUrl
        ])
      })
    } else if (typeof ga === 'object') {
      // subscribe and track like
      FB.Event.subscribe('edge.create', (targetUrl) => { ga('send', 'social', 'facebook', 'like', targetUrl) })

      // subscribe and track unlike
      FB.Event.subscribe('edge.remove', (targetUrl) => { ga('send', 'social', 'facebook', 'unlike', targetUrl) })

      // subscribe and track message
      FB.Event.subscribe('message.send', (targetUrl) => { ga('send', 'social', 'facebook', 'send', targetUrl) })
    }
  }
}
