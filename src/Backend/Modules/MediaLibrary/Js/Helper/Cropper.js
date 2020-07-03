export class Cropper {
  constructor (configSet) {
    this.config = configSet
    this.cropperQueue = []
    this.isCropping = false
    this.scaleY = 1
    this.scaleX = 1
  }

  passToCropper (resizeInfo, resolve, reject) {
    this.cropperQueue.push({
      'resizeInfo': resizeInfo,
      'resolve': resolve,
      'reject': reject
    })

    // If the cropper is already handling the queue we don't need to start it a second time.
    if (this.isCropping) {
      return
    }

    this.isCropping = true
    const $dialog = this.getDialog()
    this.switchToCropperModal($dialog)
    this.processNextImageInQueue($dialog)
  }

  getDialog () {
    const $dialog = $('[data-role=media-library-add-dialog]')

    if ($dialog.length > 0) {
      return $dialog.first()
    }

    return $('[data-role=media-library-cropper-dialog]').first()
  }

  processNextImageInQueue ($dialog) {
    const nextQueuedImage = this.cropperQueue.shift()
    this.crop(
      $dialog,
      nextQueuedImage.resizeInfo,
      nextQueuedImage.resolve,
      nextQueuedImage.reject
    )
  }

  resetScaleSettings () {
    this.scaleX = 1
    this.scaleY = 1
  }

  crop ($dialog, resizeInfo, resolve, reject) {
    this.attachEvents($dialog, resolve, reject, resizeInfo)
    this.initSourceAndTargetCanvas(
      $dialog,
      resizeInfo.sourceCanvas,
      resizeInfo.targetCanvas
    )

    this.resetScaleSettings()

    let readyCallback
    // if we don't want to show the cropper we just crop without showing it
    if (!$('[data-role="enable-cropper-checkbox"]').is(':checked')) {
      readyCallback = this.getCropEventFunction($dialog, resizeInfo, resolve)
    }

    this.initCropper($dialog, resizeInfo, readyCallback)
  }

  enableCropper () {
    $('[data-role="enable-cropper-checkbox"]').attr('checked', true)
  }

  initSourceAndTargetCanvas ($dialog, sourceCanvas, targetCanvas) {
    // set the initial height and width on the target canvas
    targetCanvas.height = sourceCanvas.height
    targetCanvas.width = sourceCanvas.width

    $dialog.find('[data-role=media-library-cropper-dialog-canvas-wrapper]').empty().append(sourceCanvas)
  }

  initCropper ($dialog, resizeInfo, readyCallback) {
    $(resizeInfo.sourceCanvas)
      .addClass('img-responsive')
      .cropper(this.getCropperConfig(readyCallback))
  }

  getCropperConfig (readyCallback) {
    const config = {
      autoCropArea: 1,
      zoomOnWheel: false,
      zoomOnTouch: false
    }

    if (readyCallback !== undefined) {
      config.ready = readyCallback
    }

    if (this.config.currentAspectRatio !== false) {
      this.config.aspectRatio = this.config.currentAspectRatio
    }

    return config
  }

  hasNextImageInQueue () {
    return this.cropperQueue.length > 0
  }

  finish ($dialog) {
    if (this.hasNextImageInQueue()) {
      // handle the next item
      this.processNextImageInQueue($dialog)

      this.switchToCropperModal($dialog)

      return
    }

    this.isCropping = false
    // check if it is a standalone dialog for the cropper
    if ($dialog.attr('data-role') === 'media-library-cropper-dialog') {
      $dialog.modal('hide')

      return
    }

    $dialog.find('[data-role=media-library-select-modal]').removeClass('d-none')
    $dialog.find('[data-role=media-library-cropper-modal]').addClass('d-none')
  }

  switchToCropperModal ($dialog) {
    if (!$('[data-role="enable-cropper-checkbox"]').is(':checked')) {
      return
    }

    if (!$dialog.hasClass('in')) {
      $dialog.modal('show')
    }

    $dialog.find('[data-role=media-library-select-modal]').addClass('d-none')
    $dialog.find('[data-role=media-library-cropper-modal]').removeClass('d-none')
  }

  getCloseEventFunction ($dialog, resizeInfo, reject) {
    return () => {
      $dialog.off('hidden.bs.modal.media-library-cropper.close')

      $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas').cropper('destroy')
      reject('Cancel')
      this.finish($dialog)
    }
  }

  getCropEventFunction ($dialog, resizeInfo, resolve) {
    return () => {
      const context = resizeInfo.targetCanvas.getContext('2d')
      const $cropper = $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas')
      const cropBoxData = $cropper.cropper('getCroppedCanvas')
      let zoomTo = 1

      // limit the width and height of the images to 3000 so they are not too big for the LiipImagine bundle
      if (cropBoxData.height > 3000 || cropBoxData.width > 3000) {
        zoomTo = 3000 / ((cropBoxData.height >= cropBoxData.width) ? cropBoxData.height : cropBoxData.width)
      }

      // set the correct height and width on the target canvas
      resizeInfo.targetCanvas.height = Math.round(cropBoxData.height * zoomTo)
      resizeInfo.targetCanvas.width = Math.round(cropBoxData.width * zoomTo)

      // make sure we start with a blank slate
      context.clearRect(0, 0, resizeInfo.targetCanvas.width, resizeInfo.targetCanvas.height)

      // add the new crop
      context.drawImage($cropper.cropper('getCroppedCanvas'), 0, 0, resizeInfo.targetCanvas.width, resizeInfo.targetCanvas.height)

      $dialog.off('hidden.bs.modal.media-library-cropper.close')
      resolve('Confirm')
      $dialog.find('[data-role=media-library-cropper-crop]').off('click.media-library-cropper.crop')
      $cropper.cropper('destroy')
      this.finish($dialog)
    }
  }

  getRotateEventFunction () {
    return () => {
      const $cropper = $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas')
      $cropper.cropper('rotate', $(this).data('degrees'))
      $cropper.cropper('crop')
    }
  }

  getZoomEventFunction () {
    return () => {
      const $cropper = $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas')
      $cropper.cropper('zoom', $(this).data('zoom'))
      $cropper.cropper('crop')
    }
  }

  getMoveEventFunction () {
    return () => {
      const $cropper = $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas')
      $cropper.cropper('move', $(this).data('x'), $(this).data('y'))
      $cropper.cropper('crop')
    }
  }

  getFlipEventFunction () {
    return () => {
      const $cropper = $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas')
      const isHorizontal = $(this).data('direction') === 'horizontal'
      const method = isHorizontal ? 'scaleX' : 'scaleY'
      this[method] = this[method] * -1

      $cropper.cropper(method, this[method])
      $cropper.cropper('crop')
    }
  }

  getResetEventFunction () {
    return () => {
      this.resetScaleSettings()
      const $cropper = $('[data-role=media-library-cropper-dialog-canvas-wrapper] > canvas')
      $cropper.cropper('reset')
      $cropper.cropper('crop')
    }
  }

  attachEvents ($dialog, resolve, reject, resizeInfo) {
    $dialog
      .off('hidden.bs.modal.media-library-cropper.close')
      .on(
        'hidden.bs.modal.media-library-cropper.close',
        this.getCloseEventFunction(
          $dialog,
          resizeInfo,
          reject
        )
      )

    $dialog.find('[data-role=media-library-cropper-crop]')
      .off('click.media-library-cropper.crop')
      .on(
        'click.media-library-cropper.crop',
        this.getCropEventFunction(
          $dialog,
          resizeInfo,
          resolve
        )
      )

    $dialog.find('[data-role=media-library-cropper-rotate]')
      .off('click.media-library-cropper.rotate')
      .on(
        'click.media-library-cropper.rotate',
        this.getRotateEventFunction()
      )

    $dialog.find('[data-role=media-library-cropper-zoom]')
      .off('click.media-library-cropper.zoom')
      .on(
        'click.media-library-cropper.zoom',
        this.getZoomEventFunction()
      )
    $dialog.find('[data-role=media-library-cropper-reset]')
      .off('click.media-library-cropper.reset')
      .on(
        'click.media-library-cropper.reset',
        this.getResetEventFunction()
      )
    $dialog.find('[data-role=media-library-cropper-flip]')
      .off('click.media-library-cropper.flip')
      .on(
        'click.media-library-cropper.flip',
        this.getFlipEventFunction()
      )
    $dialog.find('[data-role=media-library-cropper-move]')
      .off('click.media-library-cropper.move')
      .on(
        'click.media-library-cropper.move',
        this.getMoveEventFunction()
      )
  }
}
