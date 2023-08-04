import { Lightbox } from './Components/Lightbox'
import { Slider } from './Components/Slider'

export class MediaLibrary {
  constructor () {
    /* eslint-disable no-new */
    new Slider()

    // when no items for lightbox found, stop here
    if ($('.widget-media-library-lightbox').length > 0) {
      // init lightboxes
      new Lightbox('.widget-media-library-lightbox')
    }
  }
}
