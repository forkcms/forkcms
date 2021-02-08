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
      wrapperLabel: 'ce-wrapper-label',
      input: 'ce-input',
      label: 'ce-label'
    }
  }

  selectFromMediaLibrary () {
    window.open(window.location.origin + jsData.MediaLibrary.browseActionVideos)

    window.onmessage = (event) => {
      if (event.data && typeof event.data === 'object' && 'media-url' in event.data) {
        this.data.src = event.data['media-url']
        this.data.id = event.data.id
        this.image.src = 'https://img.youtube.com/vi/' + this.data.src.split('v=')[1] + '/maxresdefault.jpg'
        this.image.classList.remove('d-none')
      }
    }
  }

  render () {
    this.wrapper = this._make('div', [this.CSS.wrapper, 'media-library-video'])

    this.image = this._make('img')
    this.wrapper.appendChild(this.image)
    this.wrapper.appendChild(this.iframe)

    $(this.image).on('click.media-library-edit-image', $.proxy(this.selectFromMediaLibrary, this))

    if (this.data.src !== undefined) {
      this.image.src = this.data.src
      this.image.classList.remove('hidden')
    } else {
      this.image.src = '#'
      this.image.classList.add('d-none')
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
