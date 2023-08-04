export class MovieThumbUrl {
  set (mediaItem) {
    setTimeout(() => {
      if (mediaItem.storageType === 'youtube') {
        $('#media-' + mediaItem.id + ' [data-media-item-movie]').attr('src', 'https://img.youtube.com/vi/' + mediaItem.url + '/hqdefault.jpg')
      } else {
        this.vimeoLoadingThumb(mediaItem.url)
      }
    }, 100)
  }

  vimeoLoadingThumb (id) {
    const url = 'https://vimeo.com/api/v2/video/' + id + '.json?callback=window.backend.mediaLibrary.helper.movieThumbUrl.showThumb'

    const script = document.createElement('script')
    script.type = 'text/javascript'
    script.src = url

    $('body').append(script)
  }

  showThumb (data) {
    const $img = $('[data-video-id="' + data[0].id + '"]')
    $img.attr('src', data[0].thumbnail_medium)
  }
}
