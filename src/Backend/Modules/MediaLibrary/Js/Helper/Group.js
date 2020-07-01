import { Messages } from '../../../../Core/Js/Components/Messages'
import { Backend } from '../../../../Core/Js/Backend'
import { MediaLibraryHelper } from '../MediaLibraryHelper'
import { Duplicator } from './Duplicator'
import { Templates } from './Templates'
import { MovieThumbUrl } from './MovieThumbUrl'

export class Group {
  constructor () {
    // start or not
    if ($('[data-role=media-library-add-dialog]').length === 0) {
      return
    }

    // get galleries
    this.getGroups()

    // add media dialog
    this.addMediaDialog()

    // init sequences
    let prevSequence = ''
    let newSequence = ''

    // bind drag'n drop to media
    $('.mediaConnectedBox .ui-sortable').sortable({
      opacity: 0.6,
      cursor: 'move',
      start: (e, ui) => {
        // redefine previous and new sequence
        prevSequence = newSequence = $('#group-' + MediaLibraryHelper.currentMediaGroupId + ' .mediaIds').first().val()

        // don't prevent the click
        ui.item.removeClass('preventClick')
      },
      update: () => {
        // set group i
        MediaLibraryHelper.currentMediaGroupId = $(this).parent().parent().attr('id').replace('group-', '')

        // prepare correct new sequence value for hidden input
        newSequence = $(this).sortable('serialize').replace(/media-/g, '').replace(/\[\]=/g, '-').replace(/&/g, ',')

        // add value to hidden input
        $('#group-' + MediaLibraryHelper.currentMediaGroupId + ' .mediaIds').first().val(newSequence)
      },
      stop: (e, ui) => {
        // prevent click
        ui.item.addClass('preventClick')

        // new sequence: de-select this item + update sequence
        if (prevSequence !== newSequence) {
          // remove selected class
          ui.item.removeClass('selected')

          return
        }

        // same sequence: select this item (accidently moved this media a few millimeters counts as a click)
        // don't prevent the click, click handler does the rest
        ui.item.removeClass('preventClick')
      }
    })

    $('[data-fork=connectedItems]').on('click', '[data-fork=disconnect]', () => {
      const $mediaItem = $(this).closest('[data-fork=mediaItem]')

      this.disconnectMediaFromGroup(
        $mediaItem.data('mediaId'),
        $mediaItem.data('folderId'),
        $mediaItem.closest('[data-media-group-id]').data('mediaGroupId')
      )
    })
  }

  /**
   * Adds add media dialog, where you can connect/disconnect media to a group
   */
  addMediaDialog () {
    const $addMediaDialog = $('[data-role=media-library-add-dialog]')
    const $addMediaSubmit = $('#addMediaSubmit')

    $addMediaSubmit.on('click', () => {
      // add uploaded media to current group
      MediaLibraryHelper.upload.addUploadedMediaToGroup()

      // push media to group
      this.updateGroupMedia()

      // show message
      Messages.add('success', Backend.locale.msg('MediaGroupEdited'))

      // close the dialog
      $addMediaDialog.modal('hide')
    })

    // bind click when opening "add media dialog"
    $('.addMediaButton').on('click', (e) => {
      // prevent default
      e.preventDefault()

      // redefine folderId when clicked on other group
      if ($(e.currentTarget).data('groupId') !== MediaLibraryHelper.currentMediaGroupId || $(e.currentTarget).data('aspectRatio') !== MediaLibraryHelper.currentAspectRatio) {
        // clear folders cache
        this.clearFoldersCache()
      }

      // define groupId
      MediaLibraryHelper.currentMediaGroupId = $(this).data('groupId')
      MediaLibraryHelper.currentAspectRatio = $(this).data('aspectRatio')
      if (MediaLibraryHelper.currentAspectRatio === undefined) {
        MediaLibraryHelper.currentAspectRatio = false
      }
      MediaLibraryHelper.maximumMediaItemsCount = $(this).data('maximumMediaCount')
      if (MediaLibraryHelper.maximumMediaItemsCount === undefined) {
        MediaLibraryHelper.maximumMediaItemsCount = false
      }
      MediaLibraryHelper.minimumMediaItemsCount = $(this).data('minimumMediaCount')
      if (MediaLibraryHelper.minimumMediaItemsCount === undefined) {
        MediaLibraryHelper.minimumMediaItemsCount = false
      }

      // get current media for group
      const $currentMediaGroupMediaIds = $('#group-' + MediaLibraryHelper.currentMediaGroupId + ' .mediaIds')
      MediaLibraryHelper.currentMediaItemIds = ($currentMediaGroupMediaIds.length > 0 && $currentMediaGroupMediaIds.first().val() !== '')
        ? $.trim($('#group-' + MediaLibraryHelper.currentMediaGroupId + ' .mediaIds').first().val()).split(',') : []

      // set the group media
      MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].media = MediaLibraryHelper.currentMediaItemIds

      // load and add folders
      this.getFolders()

      // load and get folder counts for group
      this.getFolderCountsForGroup()

      // load and add media for group
      this.getMedia()

      // toggle upload boxes
      MediaLibraryHelper.upload.toggleUploadBoxes()

      // open dialog
      $addMediaDialog.modal('show')
    })

    // bind change when selecting other folder
    $('#mediaFolders').on('change', (e) => {
      // cache current folder id
      MediaLibraryHelper.mediaFolderId = $(e.currentTarget).val()

      // get media for this folder
      this.getMedia()
    })
  }

  /**
   * Clear the folders cache when necessary
   */
  clearFoldersCache () {
    MediaLibraryHelper.mediaFolders = false
  }

  /**
   * Disconnect media fast from this group
   *
   * @param {int} mediaId The media id we want to disconnect.
   * @param {int} folderId The folder of the media item we want to disconnect.
   * @param {int} groupId The group id we want to disconnect from.
   */
  disconnectMediaFromGroup (mediaId, folderId, groupId) {
    // define currentMediaGroupId
    MediaLibraryHelper.currentMediaGroupId = groupId

    // current ids
    let currentIds = $.trim($('#group-' + MediaLibraryHelper.currentMediaGroupId + ' .mediaIds').first().val()).split(',')

    // remove from array
    currentIds = jQuery.grep(currentIds, (value) => {
      return value !== mediaId
    })

    // redefine current media group
    MediaLibraryHelper.currentMediaItemIds = currentIds

    // only get new folder counts on startup
    if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].id !== 0 && MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count === undefined) {
      // load folder counts for group using ajax
      $.ajax({
        data: {
          fork: {
            module: 'MediaLibrary',
            action: 'MediaFolderGetCountsForGroup'
          },
          group_id: MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].id
        },
        success: (json, textStatus) => {
          if (json.code !== 200) {
            // show error if needed
            if (Backend.debug) {
              window.alert(textStatus)
            }

            return
          }

          // cache folder counts
          MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count = json.data

          // update group media
          this.updateGroupMedia()

          // update folder counts for items
          this.updateFolderCount(folderId, '-', 1)
        }
      })

      return
    }

    // update group media
    this.updateGroupMedia()

    // update folder counts for items
    this.updateFolderCount(folderId, '-', 1)
  }

  /**
   * @param {int} groupId
   * @returns {*|jQuery|HTMLElement}
   */
  get (groupId) {
    return $('#group-' + groupId)
  }

  /**
   * Load in the folders count for a group
   */
  getFolderCountsForGroup () {
    // only get new folder counts on startup
    if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].id !== 0 && MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count === undefined) {
      // load folder counts for group using ajax
      $.ajax({
        data: {
          fork: {
            module: 'MediaLibrary',
            action: 'MediaFolderGetCountsForGroup'
          },
          group_id: MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].id
        },
        success: (json, textStatus) => {
          if (json.code !== 200) {
            // show error if needed
            if (Backend.debug) {
              window.alert(textStatus)
            }
          } else {
            // cache folder counts
            MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count = json.data

            // update folders
            this.updateFolders()
          }
        }
      })
    }
  }

  /**
   * Load in the folders and add numConnected from the group
   */
  getFolders () {
    if (MediaLibraryHelper.mediaFolders) {
      return
    }

    // load folders using ajax
    $.ajax({
      data: {
        fork: {
          module: 'MediaLibrary',
          action: 'MediaFolderFindAll'
        }
      },
      success: (json, textStatus) => {
        if (json.code !== 200) {
          // show error if needed
          if (Backend.debug) {
            window.alert(textStatus)
          }

          return
        }

        // cache folders
        MediaLibraryHelper.mediaFolders = json.data

        // update folders
        this.updateFolders()
      }
    })
  }

  /**
   * Get groups from looking at the DOM
   */
  getGroups () {
    $('.mediaGroup').each((index, element) => {
      let activateFallback = false
      let mediaGroupId = $(element).data('media-group-id')

      // Fallback: we have no mediaGroupId
      // This means we tried saving the page, but an error was thrown, so the mediaGroup was empty again
      // But in the form field mediaIds, the ids are still present, so we need to use them and show the list items again
      if (mediaGroupId === '') {
        // redefine media group id
        mediaGroupId = $(this).find('.mediaGroupId').val()

        // Redefine wrong id (mediaGroupId was missing)
        $(this).attr('id', 'group-' + mediaGroupId)
        $(this).data('id', mediaGroupId)
        $(this).find('.addMediaButton').first().data('groupId', mediaGroupId)

        activateFallback = true
      }

      const type = $('#group-' + mediaGroupId + ' .type').first().val()
      // get current media for group
      const mediaIds = ($('#group-' + mediaGroupId + ' .mediaIds').length > 0 && $('#group-' + mediaGroupId + ' .mediaIds').first().val() !== '')
        ? $.trim($('#group-' + mediaGroupId + ' .mediaIds').first().val()).split(',') : []

      // Push ids to array
      MediaLibraryHelper.mediaGroups[mediaGroupId] = {
        'id': mediaGroupId,
        'media': mediaIds,
        'type': type
      }

      if (activateFallback && mediaIds.length > 0) {
        // load media
        $.ajax({
          data: {
            fork: {
              module: 'MediaLibrary',
              action: 'MediaItemGetAllById'
            },
            media_ids: mediaIds.join(',')
          },
          success: (json, textStatus) => {
            if (json.code !== 200) {
              // show error if needed
              if (Backend.debug) {
                window.alert(textStatus)
              }
            } else {
              // Define the constiables
              const $group = $('#group-' + mediaGroupId)
              const $holder = $group.find('.mediaConnectedItems').first()

              // Remove paragraph which says that we don't have any media connected
              $group.find('.mediaNoItems').remove()

              $(json.data.items).each((index, item) => {
                // add HTML for MediaItem to connect to holder
                $holder.append(Templates.getHTMLForMediaItemToConnect(item))
                MovieThumbUrl.set(item)
              })
            }
          }
        })
      }
    })
  }

  /**
   * Get media items in a group
   *
   * @param {int} groupId [optional]
   */
  getItems (groupId) {
    const id = (groupId) || MediaLibraryHelper.currentMediaGroupId
    return $('#group-' + id).find('.mediaConnectedItems')
  }

  /**
   * Load in the media for a group or in a folder
   */
  getMedia () {
    // Load media from cache
    if (MediaLibraryHelper.mediaFolderId === null || !MediaLibraryHelper.media[MediaLibraryHelper.mediaFolderId]) {
      this.updateMedia()
    }

    // Load media using ajax
    $.ajax({
      data: {
        fork: {
          module: 'MediaLibrary',
          action: 'MediaItemFindAll'
        },
        group_id: (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId]) ? MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].id : null,
        folder_id: MediaLibraryHelper.mediaFolderId,
        aspect_ratio: MediaLibraryHelper.currentAspectRatio
      },
      success: (json, textStatus) => {
        if (json.code !== 200) {
          // show error if needed
          if (Backend.debug) {
            window.alert(textStatus)
          }

          return
        }

        // only do this when current folder is different
        if (json.data.folder !== 0) {
          // redefine folder id
          MediaLibraryHelper.mediaFolderId = json.data.folder

          // cache media
          MediaLibraryHelper.media[MediaLibraryHelper.mediaFolderId] = json.data.media
          // redefine current folder as none
        } else {
          MediaLibraryHelper.mediaFolderId = 0
        }

        // update media
        this.updateMedia()
      }
    })
  }

  getMediaItemForId (mediaItemId) {
    let foundMediaItem = false

    $.each(MediaLibraryHelper.media, (index, mediaFolder) => {
      $.each(mediaFolder, (index, mediaItem) => {
        if (mediaItem.id === mediaItemId) {
          foundMediaItem = mediaItem

          return false
        }
      })
    })

    return foundMediaItem
  }

  updateFolderSelected () {
    // select the current media folder
    $('#mediaFolders').val(MediaLibraryHelper.mediaFolderId)
  }

  /**
   * Update the folder count
   *
   * @param {int} folderId - The folderId where you want the count to change.
   * @param {string} updateCount - Allowed: '+' or '-'
   * @param {int} updateWithValue - The value you want to add or substract.
   */
  updateFolderCount (folderId, updateCount, updateWithValue) {
    // count not found - add it
    if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count === undefined) {
      // define new object
      const obj = {}

      // redefine object
      obj[folderId] = 0

      // add object to count
      MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count = obj
    }

    // folder not found - add it to object
    if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count[folderId] === undefined) {
      // update object to count
      MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count[folderId] = 0
    }

    // init count
    let count = parseInt(MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count[folderId])

    // subtract or add value
    count = (updateCount === '-') ? (count - updateWithValue) : (count + updateWithValue)

    // redefine count when under zero
    if (count < 0) {
      count = 0
      // redefine amount when max has reached
    } else if (MediaLibraryHelper.mediaFolders[folderId] !== undefined && count > MediaLibraryHelper.mediaFolders[folderId].numMedia) {
      count = MediaLibraryHelper.mediaFolders[folderId].numMedia
    }

    // update count
    MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].count[folderId] = count

    // update folders
    this.updateFolders()
  }

  updateFolders () {
    // add folders to dropdown
    $('#mediaFolders').html(Templates.getHTMLForMediaFolders(MediaLibraryHelper.mediaFolders))

    // select the correct folder
    this.updateFolderSelected()
  }

  /**
   * Update group media hidden field
   */
  updateGroupMedia () {
    // current connected items
    const $currentItems = this.getItems()

    // current ids
    let currentIds = ($('#group-' + MediaLibraryHelper.currentMediaGroupId + ' .mediaIds').first().val() !== '')
      ? $.trim($('#group-' + MediaLibraryHelper.currentMediaGroupId + ' .mediaIds').first().val()).split(',') : []

    // define empty
    const empty = (MediaLibraryHelper.currentMediaItemIds.length === 0)

    // check which items to add
    $(MediaLibraryHelper.currentMediaItemIds).each((i, id) => {
      // add item
      if (!utils.array.inArray(id, currentIds)) {
        // loop media folders
        $.each(MediaLibraryHelper.media, (index, items) => {
          // loop media items in folder
          $.each(items, (index, item) => {
            // item found
            if (id === item.id) {
              // Add HTML for MediaItem to Connect
              $currentItems.append(Templates.getHTMLForMediaItemToConnect(item))
              MovieThumbUrl.set(item)
            }
          })
        })
      } else {
        // delete from array
        currentIds = jQuery.grep(currentIds, function (value) {
          return value !== id
        })
      }
    })

    // check which items to delete
    $(currentIds).each((i, id) => {
      // remove item
      $($currentItems).find('#media-' + id).remove()
    })

    // update the group media
    MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].media = MediaLibraryHelper.currentMediaItemIds

    // add empty media paragraph
    if (empty) {
      this.getItems().after('<p class="mediaNoItems helpTxt">' + Backend.locale.msg('MediaNoItemsConnected') + '</p>')
      // delete empty media paragraph
    } else {
      $('#group-' + MediaLibraryHelper.currentMediaGroupId).find('.mediaNoItems').remove()
      $('#group-' + MediaLibraryHelper.currentMediaGroupId).find('.media-group-type-errors').remove()
    }

    // update the hidden group field for media
    $('#group-' + MediaLibraryHelper.currentMediaGroupId).find('.mediaIds').first().val(MediaLibraryHelper.currentMediaItemIds.join(','))

    // redefine
    MediaLibraryHelper.currentMediaItemIds = []
  }

  /**
   * Update the media
   */
  updateMedia () {
    // init constiables
    const mediaItemTypes = Backend.data.get('MediaLibrary.mediaItemTypes')
    const html = {}
    const counts = {}
    const rowNoItems = Templates.getHTMLForEmptyTableRow()

    $(mediaItemTypes).each((index, type) => {
      html[type] = ''
      counts[type] = 0
    })

    // loop media
    $.each(MediaLibraryHelper.media[MediaLibraryHelper.mediaFolderId], (i, item) => {
      // check if media is connected or not
      const connected = (typeof MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId] === 'undefined') ? false : utils.array.inArray(item.id, MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].media)

      // Redefine
      html[item.type] += Templates.getHTMLForMediaItemTableRow(item, connected)
      counts[item.type] += 1
    })

    $(mediaItemTypes).each((index, type) => {
      const mediaTableHtml = '<thead><tr><th class="check"><span><input type="checkbox" name="toggleChecks" value="toggleChecks" title="Select all"></span></th>' +
        (type === 'image' ? '<th>' + utils.string.ucfirst(Backend.locale.lbl('Image')) + '</th>' : '') +
        '<th>' + utils.string.ucfirst(Backend.locale.lbl('Filename')) + '</th>' +
        '<th>' + utils.string.ucfirst(Backend.locale.lbl('Title')) + '</th>' +
        '</tr></thead>' +
        '<tbody>' + html[type] + '</tbody>'
      $('#mediaTable' + utils.string.ucfirst(type)).html((html[type]) ? $(mediaTableHtml) : rowNoItems)
      $('#mediaCount' + utils.string.ucfirst(type)).text('(' + counts[type] + ')')
    })

    // Init toggle for mass-action checkbox
    Backend.controls.bindMassCheckbox()

    // init $tabs
    const $tabs = $('#tabLibrary').find('.nav-tabs')

    // remove selected
    $tabs.find('.active').removeClass('active')

    // not in connect-to-group modus (just uploading)
    if (typeof MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId] === 'undefined') {
      return false
    }

    // Enable all because we can switch between different groups on the same page
    $tabs.children('.nav-link').removeClass('disabled, active')

    let disabled = ''
    let enabled = '.nav-item:eq(0)'

    // we have an image group
    if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].type === 'image') {
      disabled = '.nav-item:gt(0)'
    } else if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].type === 'file') {
      disabled = '.nav-item:eq(0), .nav-item:eq(2), .nav-item:eq(3)'
      enabled = '.nav-item:eq(1)'
    } else if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].type === 'movie') {
      disabled = '.nav-item:eq(0), .nav-item:eq(1), .nav-item:eq(3)'
      enabled = '.nav-item:eq(2)'
    } else if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].type === 'audio') {
      disabled = '.nav-item:lt(3)'
      enabled = '.nav-item:eq(3)'
    } else if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].type === 'image-file') {
      disabled = '.nav-item:eq(2), .nav-item:eq(3)'
    } else if (MediaLibraryHelper.mediaGroups[MediaLibraryHelper.currentMediaGroupId].type === 'image-movie') {
      disabled = '.nav-item:eq(1), .nav-item:eq(3)'
    }

    if (disabled !== '') {
      $tabs.children(disabled).find('.nav-link').addClass('disabled')
    }
    $tabs.children(enabled).children('a').attr('data-toggle', 'tab').first().tab('show')

    // get table
    const $tables = $('.mediaTable')

    // bind change when connecting/disconnecting media
    $tables.find('.toggleConnectedCheckbox').on('change', (e) => {
      // mediaId
      const mediaId = $(e.currentTarget).parent().parent().attr('id').replace('media-', '')

      // Is the checkbox checked or unchecked?
      const isChecked = $(e.currentTarget).is(':checked')

      // was already connected?
      const connected = utils.array.inArray(mediaId, MediaLibraryHelper.currentMediaItemIds)

      // delete from array
      if (connected && !isChecked) {
        // loop all to find value and to delete it
        MediaLibraryHelper.currentMediaItemIds.splice(MediaLibraryHelper.currentMediaItemIds.indexOf(mediaId), 1)

        // update folder count
        this.updateFolderCount(MediaLibraryHelper.mediaFolderId, '-', 1)
        // add to array
      } else if (!connected && isChecked) {
        MediaLibraryHelper.currentMediaItemIds.push(mediaId)

        // update folder count
        this.updateFolderCount(MediaLibraryHelper.mediaFolderId, '+', 1)
      }

      // validate the minimum and maximum count
      this.validateMinimumMaximumCount()
    })

    // bind click to duplicate media item
    $('[data-role=media-library-duplicate-and-crop]').on('click', () => {
      const mediaItemToDuplicate = this.getMediaItemForId($(this).data('media-item-id'))
      new Duplicator(mediaItemToDuplicate)
    })

    // select the correct folder
    this.updateFolderSelected()

    // validate the minimum and maximum count
    this.validateMinimumMaximumCount()
  }

  /**
   * Runs the validation for the minimum and maximum count of connected media
   */
  validateMinimumMaximumCount () {
    const totalMediaCount = MediaLibraryHelper.upload.uploadedCount + MediaLibraryHelper.currentMediaItemIds.length
    const $minimumCountError = $('[data-role="fork-media-count-error"]')
    const $submitButton = $('#addMediaSubmit')

    if (MediaLibraryHelper.maximumMediaItemsCount !== false && totalMediaCount > MediaLibraryHelper.maximumMediaItemsCount) {
      $minimumCountError.html(Backend.locale.err('MaximumConnectedItems').replace('{{ limit }}', MediaLibraryHelper.maximumMediaItemsCount)).removeClass('d-none')
      $submitButton.addClass('disabled').attr('disabled', true)

      return
    }

    if (MediaLibraryHelper.minimumMediaItemsCount !== false && totalMediaCount < MediaLibraryHelper.minimumMediaItemsCount) {
      $minimumCountError.html(Backend.locale.err('MinimumConnectedItems').replace('{{ limit }}', MediaLibraryHelper.minimumMediaItemsCount)).removeClass('d-none')
      $submitButton.addClass('disabled').attr('disabled', true)

      return
    }

    $minimumCountError.html('').addClass('d-none')
    $submitButton.removeClass('disabled').attr('disabled', false)
  }
}
