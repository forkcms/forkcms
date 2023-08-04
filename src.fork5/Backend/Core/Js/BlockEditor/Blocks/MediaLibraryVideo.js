class MediaLibraryVideo {
  constructor ({data}) {
    this.data = data
  }

  static get toolbox () {
    return {
      title: 'Video',
      icon: '<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px" height="15" width="21.33" viewBox="0 0 546.1 384" style="enable-background:new 0 0 546.1 384;" xml:space="preserve"><path d="M534.7,60.1c-6.3-23.7-24.8-42.3-48.3-48.6C443.8,0,273.1,0,273.1,0S102.3,0,59.7,11.5c-23.5,6.3-42,24.9-48.3,48.6C0,102.9,0,192.4,0,192.4s0,89.4,11.4,132.3c6.3,23.6,24.8,41.5,48.3,47.8C102.3,384,273.1,384,273.1,384s170.8,0,213.4-11.5c23.5-6.3,42-24.2,48.3-47.8c11.4-42.9,11.4-132.3,11.4-132.3S546.1,102.9,534.7,60.1z M217.2,273.6V111.2L360,192.4L217.2,273.6L217.2,273.6z"/></svg>'
    }
  }

  /**
   * CSS classes
   *
   * @returns {{wrapper: string, wrapperLabel: string, input: string, label: string, settingsWrapper: string, settingsButton: string, settingsButtonActive: string}}
   */
  get CSS () {
    return {
      wrapper: 'ce-wrapper',
      videoWrapper: ['ce-video-wrapper', 'd-none'],
      videoWrapperEditButton: ['btn', 'btn-primary', 'btn-sm', 'btn-icon-only', 'ce-btn-edit']
    }
  }

  getIframeSrc (id) {
    return 'https://www.youtube-nocookie.com/embed/' + id
  }

  selectFromMediaLibrary () {
    window.open(window.location.origin + jsData.MediaLibrary.browseActionVideos)

    window.onmessage = (event) => {
      if (event.data && typeof event.data === 'object' && 'media-url' in event.data) {
        this.data.videoId = event.data['media-url'].split('v=')[1]
        this.data.id = event.data.id
        this.iframe.src = this.getIframeSrc(this.data.videoId)
        this.videoWrapper.classList.remove('d-none')
      }
    }
  }

  render () {
    this.wrapper = this._make('div', this.CSS.wrapper)

    this.videoWrapper = this._make('div', this.CSS.videoWrapper)
    this.iframeWrapper = this._make('div', ['embed-responsive', 'embed-responsive-16by9'])
    this.iframe = this._make('iframe', ['embed-responsive-item'], {},
      {
        'frameborder': '0',
        'allow': 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture'
      }
    )

    this.editButton = this._make('button', this.CSS.videoWrapperEditButton, {
      type: 'button'
    })
    this.editButtonIcon = this._make('i', ['fas', 'fa-pencil-alt'])

    this.editButton.appendChild(this.editButtonIcon)
    this.iframeWrapper.appendChild(this.iframe)
    this.videoWrapper.appendChild(this.iframeWrapper)
    this.videoWrapper.appendChild(this.editButton)
    this.wrapper.appendChild(this.videoWrapper)

    $(this.editButton).on('click', $.proxy(this.selectFromMediaLibrary, this))

    if (this.data.videoId !== undefined) {
      this.iframe.src = this.getIframeSrc(this.data.videoId)
      this.videoWrapper.classList.remove('d-none')
    } else {
      this.iframe.src = '#'
      this.videoWrapper.classList.add('d-none')
      this.selectFromMediaLibrary()
    }

    return this.wrapper
  }

  save (blockContent) {
    return {
      'id': this.data.id,
      'videoId': this.data.videoId
    }
  }

  /**
   * Helper for making Elements with attributes
   *
   * @param  {string} tagName           - new Element tag name
   * @param  {Array|string} classNames  - list or name of CSS classname(s)
   * @param  {object} attributes        - any attributes
   * @returns {Element}
   */
  _make (tagName, classNames = null, attributes = {}, customAttributes = {}) {
    const el = document.createElement(tagName)

    if (Array.isArray(classNames)) {
      el.classList.add(...classNames)
    } else if (classNames) {
      el.classList.add(classNames)
    }

    for (const attrName in attributes) {
      el[attrName] = attributes[attrName]
    }

    for (const attrName in customAttributes) {
      el.setAttribute(attrName, customAttributes[attrName])
    }

    return el
  }
}

export default MediaLibraryVideo
