/**
 * All methods related to duplicating an existing media item
 * which also show the crop tool in the process
 */
import { Cropper } from './Cropper'

export class Duplicator {
  constructor (mediaItemToDuplicate) {
    if (!mediaItemToDuplicate) {
      return
    }

    const cropper = new Cropper()

    // create canvas
    const canvas = document.createElement('canvas')
    const context = canvas.getContext('2d')
    canvas.height = mediaItemToDuplicate.height
    canvas.width = mediaItemToDuplicate.width

    // create image
    const image = new window.Image()
    image.onload = () => {
      context.drawImage(this, 0, 0)

      // enable cropper
      cropper.enableCropper()

      // switch from "library"-tab to "upload"-tab
      const tab = document.querySelectorAll('.nav-tabs a[href="#tabUploadMedia"]')[0]
      const tabObject = new bootstrap.Tab(tab)
      tabObject.show()

      // let FineUploader handle the file
      const splittedUrl = mediaItemToDuplicate.url.split('.')
      $('#fine-uploader-gallery').fineUploader('addFiles', [{
        'canvas': canvas,
        'name': splittedUrl[0] + '-2.' + splittedUrl[1],
        'mime': mediaItemToDuplicate.mime
      }])
    }
    image.src = mediaItemToDuplicate.source
  }
}
