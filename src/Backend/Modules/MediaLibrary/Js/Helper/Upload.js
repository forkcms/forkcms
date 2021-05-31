import { Messages } from '../../../../Core/Js/Components/Messages'
import { Data } from '../../../../Core/Js/Components/Data'
import { Config } from '../../../../Core/Js/Components/Config'
import { StringUtil } from '../../../../Core/Js/Components/StringUtil'
import { Templates } from './Templates'

export class Upload {
  constructor (configSet) {
    this.config = configSet
    this.uploadedCount = 0

    this.preInit()

    // redefine media folder id
    this.config.mediaFolderId = $('#uploadMediaFolderId').val()

    // check if we need to cropper is mandatory
    this.toggleCropper()

    const $fineUploaderGallery = $('#fine-uploader-gallery')
    const fineUploaderInstance = $fineUploaderGallery.fineUploader({
      template: 'qq-template-gallery',
      options: {
        request: {
          omitDefaultParams: true
        }
      },
      chunking: {
        enabled: true,
        success: {
          endpoint: function () {
            var mediaFolderId = $('#uploadMediaFolderId').val()

            return '/backend/ajax?fork[module]=MediaLibrary&fork[action]=MediaItemUpload&fork[language]=' +
              jsBackend.current.language + '&folder_id=' + mediaFolderId + '&done=1'
          }
        },
        concurrent: {
          enabled: true
        }
      },
      thumbnails: {
        placeholders: {
          waitingPath: '/css/vendors/fine-uploader/waiting-generic.png',
          notAvailablePath: '/css/vendors/fine-uploader/not_available-generic.png'
        }
      },
      validation: {
        allowedExtensions: Data.get('MediaLibrary.mediaAllowedExtensions')
      },
      scaling: this.getScalingConfig(),
      callbacks: {
        onUpload: () => {
          // redefine media folder id
          this.config.mediaFolderId = $('#uploadMediaFolderId').val()

          // We must set the endpoint dynamically, because "uploadMediaFolderId" is null at start and is async loaded using AJAX.
          fineUploaderInstance.fineUploader('setEndpoint', '/backend/ajax?fork[module]=MediaLibrary&fork[action]=MediaItemUpload&fork[language]=' + Config.getCurrentLanguage() + '&folder_id=' + this.config.mediaFolderId)
          fineUploaderInstance.fineUploader('setCustomHeaders', {'X-CSRF-Token': Data.get('csrf-token')})
        },
        onComplete: (id, name, responseJSON) => {
          // add file to uploaded box
          $('#uploadedMedia').append(Templates.getHTMLForUploadedMediaItem(responseJSON))

          // update counter
          this.uploadedCount += 1

          // toggle upload box
          this.toggleUploadBoxes()

          $fineUploaderGallery.find('.qq-upload-success[qq-file-id=' + id + ']').hide()

          // Add select button if tab in selection context
          if ($('#tabUploadMedia').data('context') === 'selection') {
            const $link = $('<a href="#" class="btn btn-success btn-sm btn-icon-only addUploadedMediaItem" data-direct-url="' +
              responseJSON.direct_url + '"><span class="visually-hidden">' + StringUtil.ucfirst(window.backend.locale.lbl('Select')) + '</span><i class="fas fa-check fa-fw" aria-hidden="true"></i></a>')

            $link.on('click', window.backend.mediaLibrary.helper.modalSelection.sendToParent)
            $('li[id="media-' + responseJSON.id + '"]').find('.mediaHolder')
              .append($link)
          }
        },
        onAllComplete: (succeeded, failed) => {
          // clear if already exists
          if (this.config.media[this.config.mediaFolderId]) {
            // set folder to false so we can refresh items in the folder
            this.config.media[this.config.mediaFolderId] = false
          }

          // load and add media for group
          window.backend.mediaLibrary.helper.group.getMedia()

          // everything uploaded, show success message
          if (failed.length === 0) {
            Messages.add('success', StringUtil.sprintf(window.backend.locale.msg('MediaUploadedSuccess'), succeeded.length))
            // not everything is uploaded successful, show error message
          } else {
            Messages.add('danger', StringUtil.sprintf(window.backend.locale.err('MediaUploadedError'), (succeeded.length + '","' + failed.length)))
          }
        }
      }
    })
  }

  preInit () {
    // bind change to upload_type
    $('#uploadMediaTypeBox').on('change', 'input[name=uploading_type]', $.proxy(this.toggleUploadBoxes, this))

    // bind click to add movie
    $('#addMediaMovie').on('click', $.proxy(this.insertMovie, this))

    // bind change to upload folder
    $('#uploadMediaFolderId').on('change', () => {
      // update upload button
      this.toggleUploadBoxes()
    }).trigger('change')

    // bind delete actions
    $('#uploadedMedia').on('click', '[data-fork=disconnect]', (e) => {
      $(e.currentTarget).parent().parent().remove()
      --this.uploadedCount
      window.backend.mediaLibrary.helper.group.validateMinimumMaximumCount()
    })
  }

  toggleCropper () {
    // the cropper is mandatory
    const $formGroup = $('[data-role="cropper-is-mandatory-form-group"]')
    const $warning = $('[data-role="cropper-is-mandatory-message"]')
    const $checkbox = $('[data-role="enable-cropper-checkbox"]')

    if (this.config.currentAspectRatio === false) {
      $formGroup.removeClass('has-warning')
      $warning.addClass('d-none')
      $checkbox.removeClass('disabled').attr('disabled', false).attr('checked', false)

      return
    }

    $formGroup.addClass('has-warning')
    $warning.removeClass('d-none')
    $checkbox.addClass('disabled').attr('disabled', true).attr('checked', true)
  }

  /**
   * Configure the uploader to trigger the cropper
   */
  getScalingConfig () {
    return {
      includeExif: false, // needs to be false to prevent issues during the cropping process, it also is good for privacy reasons
      sendOriginal: false,
      sizes: [
        {name: '', maxSize: 1} // used to trigger the cropper, this will set the maximum resulution to 1x1 px
                               // It always trigger the cropper since it uses a hook for scaling pictures down
      ],
      customResizer: function (resizeInfo) {
        return new Promise(function (resolve, reject) {
          window.backend.mediaLibrary.helper.cropper.passToCropper(resizeInfo, resolve, reject)
        })
      }
    }
  }

  /**
   * Add uploaded media to group
   */
  addUploadedMediaToGroup () {
    // loop remaining items in uploaded media and push them to current group
    $('#uploadedMedia').find('li').each((key, element) => {
      // get id
      const id = $(element).attr('id').replace('media-', '')

      // add each id to array
      this.config.currentMediaItemIds.push(id)
    })

    // clear upload queue count
    this.uploadedCount = 0

    // clear all elements
    $('#uploadedMedia').html('')
  }

  /**
   * Insert movie
   *
   * @param {Event} e
   */
  insertMovie (e) {
    // prevent other functions
    e.preventDefault()

    // update media for folder
    this.config.mediaFolderId = $('#uploadMediaFolderId').val()

    // define variables
    const storageType = $('#mediaMovieStorageType').find(':checked').val()
    const $id = $('#mediaMovieId')
    const $title = $('#mediaMovieTitle')

    // insert movie using ajax
    $.ajax({
      data: {
        fork: {
          module: 'MediaLibrary',
          action: 'MediaItemAddMovie'
        },
        folder_id: this.config.mediaFolderId,
        storageType: storageType,
        id: $id.val(),
        title: $title.val()
      },
      success: (json, textStatus) => {
        if (json.code !== 200) {
          // show error if needed
          if (Config.isDebug()) {
            window.alert(textStatus)
          }
        } else {
          // add uploaded movie
          $('#uploadedMedia').append(Templates.getHTMLForUploadedMediaItem(json.data))

          // update counter
          this.uploadedCount += 1

          // toggle upload boxes
          this.toggleUploadBoxes()

          // clear if already exists
          if (this.config.media[this.config.mediaFolderId]) {
            // set folder to false so we can refresh items in the folder
            this.config.media[this.config.mediaFolderId] = false
          }

          // load and add media for group
          window.backend.mediaLibrary.helper.group.getMedia()

          // Clear the fields
          $id.val('')
          $title.val('')

          // show message
          Messages.add('success', window.backend.locale.msg('MediaMovieIsAdded'))

          // Add select button if tab in selection context
          if ($('#tabUploadMedia').data('context') === 'selection') {
            const $link = $('<a href="#" class="btn btn-success  btn-sm btn-icon-only addUploadedMediaItem" data-direct-url="' + json.data.direct_url + '"><span class="visually-hidden">' + StringUtil.ucfirst(window.backend.locale.lbl('Select')) + '</span><i class="fas fa-check fa-fw" aria-hidden="true"></i></i></a>')
            $link.on('click', window.backend.mediaLibrary.helper.modalSelection.sendToParent)
            $('li[id="media-' + json.data.id + '"]').find('.mediaHolder.mediaHolderMovie')
              .append($link)
          }
        }
      }
    })
  }

  /**
   * Toggle the upload box + uploaded box
   * Depending on the selected folder and the amount of files in the queue.
   */
  toggleUploadBoxes () {
    // init variables
    const $uploadingType = $('#uploadMediaTypeBox input[name=uploading_type]')
    const folderSelected = ($('#uploadMediaFolderId').val() !== 0)
    let showMediaTypeBox = false // step 2
    let showMediaBox = false // step 2
    let showMovieBox = false // step 2
    let showUploadedBox = false // step 3

    // define group type
    const groupType = (this.config.mediaGroups[this.config.currentMediaGroupId]) ? this.config.mediaGroups[this.config.currentMediaGroupId].type : 'all'

    // does group accepts movies
    const moviesAllowed = (groupType === 'all' || groupType === 'image-movie' || groupType === 'movie')

    // movies not allowed
    if (!moviesAllowed) {
      // select first item (which is all, so we can upload regular media)
      $uploadingType.find(':first-child').attr('checked', 'checked')
    } else {
      if (groupType === 'movie') {
        // select first item (which is all, so we can upload regular media)
        $uploadingType.eq(1).attr('checked', 'checked')
      } else {
        showMediaTypeBox = true
      }
    }

    // if we have media uploaded, show the uploaded box
    if (this.uploadedCount > 0) showUploadedBox = true

    // we want to upload media
    if ($uploadingType.filter(':checked').val() === 'all') {
      // if we have selected a folder, show the upload media box
      if (folderSelected) showMediaBox = true
      // we want to add movies (from youtube, ...)
    } else {
      // if we have selected a folder, show the upload media box
      if (folderSelected) showMovieBox = true
    }

    // update show upload type choise
    $('#uploadMediaTypeBox').toggle(showMediaTypeBox)

    // toggle upload media box
    $('#uploadMediaBox').toggle(showMediaBox)

    // toggle upload movie box
    $('#addMovieBox').toggle(showMovieBox)

    // toggle uploaded box
    $('#uploadedMediaBox').toggle(showUploadedBox)
    $('#mediaWillBeConnectedToMediaGroup').toggle((this.config.currentMediaGroupId !== 0))

    if (this.uploadedCount === 0) {
      $('[data-role=uploadMediaStep1]').show()
      $('[data-role=uploadMediaStep2]').hide()
    } else {
      $('[data-role=uploadMediaStep1]').hide()
      $('[data-role=uploadMediaStep2]').show()
    }

    $('[data-role=uploadMediaGoToStep2]').on('click', function () {
      $('[data-role=uploadMediaStep1]').hide()
      $('[data-role=uploadMediaStep2]').show()
    })
  }
}
