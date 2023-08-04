export class Statistics {
  constructor () {
    this.trackOutboundLinks()
  }

  // track all outbound links
  trackOutboundLinks () {
    // check if Google Analytics is available
    if (typeof _gaq === 'object' || typeof ga === 'function') {
      // create a new selector
      $.expr[':'].external = (obj) => {
        return (typeof obj.href !== 'undefined') && (obj.hostname !== window.location.hostname)
      }

      // bind on all links that don't have the class noTracking
      $(document).on('click', 'a:external:not(.noTracking)', (e) => {
        // only simulate direct links
        const hasTarget = (typeof $(e.currentTarget).attr('target') !== 'undefined')
        if (!hasTarget) e.preventDefault()

        const link = $(e.currentTarget).attr('href')

        // outbound link by default
        let type = 'Outbound Links'
        let pageView = '/Outbound Links/' + link

        // set mailto
        if (link.match(/^mailto:/)) {
          type = 'Mailto'
          pageView = '/Mailto/' + link.substring(7)
        }

        // set anchor
        if (link.match(/^#/)) {
          type = 'Anchors'
          pageView = '/Anchor/' + link.substring(1)
        }

        // track in Google Analytics
        if (typeof _gaq === 'object') {
          _gaq.push(['_trackEvent', type, pageView])
        } else {
          ga('send', 'event', type, pageView)
        }

        // set time out
        if (!hasTarget) setTimeout(() => { document.location.href = link }, 100)
      })
    }
  }
}
