class Theme
  # Class methods
  @events: (events) ->
    @::events ?= {}
    @::events = $.extend({}, @::events) unless @::hasOwnProperty "events"
    #@::events[key] = value for key, value of events
    @::events = $.extend(true, {}, @::events, events)

  @onDomReady: (initializers) ->
    @::onDomReady ?= []
    @::onDomReady = @::onDomReady[..] unless @::hasOwnProperty "onDomReady"
    @::onDomReady.push initializer for initializer in initializers

  constructor: ->
    @_setupEventListeners()

  domReady: ->
    @_loadOnDomReadyMethods()

  _loadOnDomReadyMethods: ->
    for callback in @onDomReady
      @[callback]()

  _setupEventListeners: =>
    $document = $(document)
    for selector, actions of @events
      for action, callback of actions
        $document.on(action, selector, @[callback])

class DefaultTheme extends Theme
  @events
    '#cookieBar .close' : click : 'cookieBarHide'
    '#cookieBarAgree' : click : 'cookieBarAgree'
    '#cookieBarDisagree' : click : 'cookieBarDisagree'
    'input:submit' : click : 'hijackSubmit'
    'a.backToTop': click : 'scrollToTop'
    'a[href*="#"]': click : 'scrollTo'

  @onDomReady [
    'cookieBar'
    'removeImageHeight'
    'initCarouselWithSwipe'
  ]

  cookieBar: ->
    if utils.cookies.readCookie('cookie_bar_hide') == 'b%3A1%3B'
      $('#cookieBar').remove()

  cookieBarAgree: ->
    utils.cookies.setCookie('cookie_bar_agree', 'b:1;')
    utils.cookies.setCookie('cookie_bar_hide', 'b:1;')
    $('#cookieBar').alert('close')
    if ga?
      ga(
          'send',
          'event',
          'cookiebar',
          'agree'
      )

    if _gaq?
      _gaq.push([
        '_trackEvent',
        'cookiebar',
        'agree'
      ]);
    false

  cookieBarDisagree: ->
    utils.cookies.setCookie('cookie_bar_agree', 'b:0;')
    utils.cookies.setCookie('cookie_bar_hide', 'b:1;')
    $('#cookieBar').alert('close')
    if ga?
      ga(
          'send',
          'event',
          'cookiebar',
          'disagree'
      )

    if _gaq?
      _gaq.push([
        '_trackEvent',
        'cookiebar',
        'disagree'
      ]);

    false

  cookieBarHide: ->
    if ga?
      ga(
        'send',
        'event',
        'cookiebar',
        'hide'
      )

    if _gaq?
      _gaq.push([
        '_trackEvent',
        'cookiebar',
        'hide'
      ]);
    false

  hijackSubmit: (e) ->
    $(@).addClass('loading')

  scrollToTop: (e) ->
    $('html, body').stop().animate(scrollTop: 0, 600)
    false

  scrollTo: (e) =>
    $anchor = $(e.currentTarget)
    href = $anchor.attr('href')
    url = href.substr(0, href.indexOf('#'))
    hash = href.substr(href.indexOf('#'))

    # check if we have an url, and if it is on the current page and the element exists
    if  (url == '' or
        url.indexOf(document.location.pathname) >= 0) and
        not $anchor.is('[data-no-scroll]') and
        $(hash).length > 0

      $('html, body').stop().animate({
        scrollTop: $(hash).offset().top
      }, 600, =>
        @setFocus(hash)
      )
      false

  removeImageHeight: ->
    $('img').css(height: 'auto').addClass('img-responsive')

  initCarouselWithSwipe: ->
    $('.carousel').on 'swipeleft', (e)->
      $(e.currentTarget).carousel('next')
    $('.carousel').on 'swiperight', (e)->
      $(e.currentTarget).carousel('prev')

  setFocus: (hash) ->
    if $(hash).find('.nonVisibleAnchor').length > 0
      $(hash).find('.nonVisibleAnchor').focus()
    else
      false

class SpecificTheme extends DefaultTheme
  @events
    # '#element' : event : 'functionName'

  @onDomReady [
    #'functionName'
  ]

  # Request AnimationFrame Polyfill

  window.requestAnimationFrame = (->
    lastTime = 0

    window.requestAnimationFrame ||
    window.webkitRequestAnimationFrame ||
    window.mozRequestAnimationFrame ||
    window.oRequestAnimationFrame ||
    window.msRequestAnimationFrame ||
    (callback, element) ->
      curTime = new Date().getTime()
      timeToCall = Math.max(0, 16 - (curTime - lastTime))
      id = window.setTimeout(
        -> callback(curTime + timeToCall)
      , timeToCall)
      lastTime = curTime + timeToCall
      return id
  )()

  # Define functions here



SpecificTheme.current = new SpecificTheme()

$ ->
  SpecificTheme.current.domReady()

window.SpecificTheme = SpecificTheme
