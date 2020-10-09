class MediaLibraryImage {
  constructor ({data}) {
    this.data = data
  }

  static get toolbox () {
    return {
      title: 'Image',
      icon: '<svg width="17" height="15" viewBox="0 0 336 276" xmlns="http://www.w3.org/2000/svg"><path d="M291 150V79c0-19-15-34-34-34H79c-19 0-34 15-34 34v42l67-44 81 72 56-29 42 30zm0 52l-43-30-56 30-81-67-66 39v23c0 19 15 34 34 34h178c17 0 31-13 34-29zM79 0h178c44 0 79 35 79 79v118c0 44-35 79-79 79H79c-44 0-79-35-79-79V79C0 35 35 0 79 0z"/></svg>'
    }
  }

  selectFromMediaLibrary () {
    window.open(window.location.origin + jsData.MediaLibrary.browseActionImages)

    window.onmessage = function (event) {
      if (event.data && typeof event.data === 'object' && 'media-url' in event.data) {
        this.data.src = event.data['media-url']
        this.data.id = event.data.id
        this.image.src = this.data.src
        this.image.classList.remove('hidden')
      }
    }
  }

  render () {
    this.wrapper = document.createElement('div')
    this.wrapper.classList.add('media-library-image')

    this.image = document.createElement('img')
    this.wrapper.appendChild(this.image)
    $(this.image).on('click.media-library-edit-image', () => this.selectFromMediaLibrary(this))
    this.image.classList.add('img-responsive')
    this.image.style.cursor = 'pointer'

    if (this.data.src !== undefined) {
      this.image.src = this.data.src
      this.image.classList.remove('hidden')
    } else {
      this.image.src = '#'
      this.image.classList.add('hidden')
      this.selectFromMediaLibrary(this)
    }

    return this.wrapper
  }

  save (blockContent) {
    return {
      'id': this.data.id,
      'src': this.data.src
    }
  }
}

export default MediaLibraryImage
