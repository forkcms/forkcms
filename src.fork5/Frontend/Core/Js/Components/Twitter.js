import {UrlUtil} from '../../../../Backend/Core/Js/Components/UrlUtil'

export class Twitter {
  constructor () {
    // if GA is integrated and a tweet button is used
    if (typeof twttr === 'object' && (typeof _gaq === 'object' || typeof ga === 'object')) {
      // bind event, so we can track the tweets
      twttr.events.on('tweet', (e) => {
        // valid event?
        if (e) {
          // init var
          let targetUrl = null

          // get url
          if (e.target && e.target.nodeName === 'IFRAME') targetUrl = UrlUtil.extractParamFromUri(e.target.src, 'url')

          // push to GA
          if (typeof _gaq === 'object') {
            _gaq.push(['_trackSocial', 'twitter', 'tweet', targetUrl])
          } else {
            ga('send', 'social', 'twitter', 'tweet', targetUrl)
          }
        }
      })
    }
  }
}
