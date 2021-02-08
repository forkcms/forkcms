class MediaLibraryVideo {
  constructor ({data}) {
    this.data = data
  }

  static get toolbox () {
    return {
      title: 'Video',
      icon: '<svg width="17" height="15" viewBox="0 0 336 276" xmlns="http://www.w3.org/2000/svg"><path d="M291 150V79c0-19-15-34-34-34H79c-19 0-34 15-34 34v42l67-44 81 72 56-29 42 30zm0 52l-43-30-56 30-81-67-66 39v23c0 19 15 34 34 34h178c17 0 31-13 34-29zM79 0h178c44 0 79 35 79 79v118c0 44-35 79-79 79H79c-44 0-79-35-79-79V79C0 35 35 0 79 0z"/></svg>'
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

  selectFromMediaLibrary () {
    window.open(window.location.origin + jsData.MediaLibrary.browseActionVideos)

    window.onmessage = (event) => {
      if (event.data && typeof event.data === 'object' && 'media-url' in event.data) {
        this.data.src = 'https://www.youtube-nocookie.com/embed/' + event.data['media-url'].split('v=')[1]
        this.data.id = event.data.id
        this.iframe.src = this.data.src
        this.videoWrapper.classList.remove('d-none')
      }
    }
  }

  render () {
    this.wrapper = this._make('div', [this.CSS.wrapper])

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

    if (this.data.src !== undefined) {
      this.iframe.src = this.data.src
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
      'src': this.data.src
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
