/**
 * All methods related to duplicating an existing media item
 * which also show the crop tool in the process
 * global: jsBackend
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
      $('.nav-tabs a[href="#tabUploadMedia"]').tab('show')

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
