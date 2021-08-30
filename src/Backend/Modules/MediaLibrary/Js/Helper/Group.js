import { Messages } from '../../../../Core/Js/Components/Messages'
import { Duplicator } from './Duplicator'
import { Templates } from './Templates'
import { Config } from '../../../../Core/Js/Components/Config'
import { Data } from '../../../../Core/Js/Components/Data'
import { StringUtil } from '../../../../Core/Js/Components/StringUtil'
import Sortable from 'sortablejs'
import { EventUtil } from '../../../../Core/Js/Components/EventUtil'

export class Group {
  constructor (configSet) {
    this.config = configSet

    // start or not
    if ($('[data-role=media-library-add-dialog]').length === 0) {
      return
    }

    // get galleries
    this.getGroups()

    // add media dialog
    this.addMediaDialog()

    // edit media dialog
    this.editMediaDialog()

    // init sequences
    let newSequence = ''
    const element = document.querySelector('[data-sequence-drag-and-drop="media-connected"]')

    // bind drag'n drop to media
    const sortable = new Sortable(element, {
      onStart: (event) => {
        // redefine previous and new sequence
        newSequence = $('#group-' + this.config.currentMediaGroupId + ' .mediaIds').first().val()
      },
      onUpdate: (event) => {
        // set group i
        this.config.currentMediaGroupId = $(event).parents('[data-media-group]').data('media-group-id')

        // prepare correct new sequence value for hidden input
        newSequence = sortable.toArray().join(',')

        // add value to hidden input
        $('#group-' + this.config.currentMediaGroupId + ' .mediaIds').first().val(newSequence)
      }
    })

    $('[data-fork=connectedItems]').on('click', '[data-fork=disconnect]', (event) => {
      const $mediaItem = $(event.currentTarget).closest('[data-fork=mediaItem]')

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
    const addMediaModal = new window.bootstrap.Modal(document.querySelectorAll('[data-role=media-library-add-dialog]')[0])
    const $addMediaSubmit = $('#addMediaSubmit')

    $addMediaSubmit.on('click', () => {
      // add uploaded media to current group
      window.backend.mediaLibrary.helper.upload.addUploadedMediaToGroup()

      // push media to group
      this.updateGroupMedia()

      // show message
      Messages.add('success', window.backend.locale.msg('MediaGroupEdited'))

      // close the dialog
      addMediaModal.hide()
    })

    // bind click when opening "add media dialog"
    $('.addMediaButton').on('click', (e) => {
      // prevent default
      e.preventDefault()

      // redefine folderId when clicked on other group
      if ($(e.currentTarget).data('groupId') !== this.config.currentMediaGroupId || $(e.currentTarget).data('aspectRatio') !== this.config.currentAspectRatio) {
        // clear folders cache
        this.clearFoldersCache()
      }

      // define groupId
      this.config.currentMediaGroupId = $(e.currentTarget).data('groupId')
      this.config.currentAspectRatio = $(e.currentTarget).data('aspectRatio')
      if (this.config.currentAspectRatio === undefined) {
        this.config.currentAspectRatio = false
      }
      this.config.maximumMediaItemsCount = $(e.currentTarget).data('maximumMediaCount')
      if (this.config.maximumMediaItemsCount === undefined) {
        this.config.maximumMediaItemsCount = false
      }
      this.config.minimumMediaItemsCount = $(e.currentTarget).data('minimumMediaCount')
      if (this.config.minimumMediaItemsCount === undefined) {
        this.config.minimumMediaItemsCount = false
      }

      // get current media for group
      const $currentMediaGroupMediaIds = $('#group-' + this.config.currentMediaGroupId + ' .mediaIds')
      this.config.currentMediaItemIds = ($currentMediaGroupMediaIds.length > 0 && $currentMediaGroupMediaIds.first().val() !== '')
        ? $.trim($('#group-' + this.config.currentMediaGroupId + ' .mediaIds').first().val()).split(',') : []

      // set the group media
      this.config.mediaGroups[this.config.currentMediaGroupId].media = this.config.currentMediaItemIds

      // load and add folders
      this.getFolders()

      // load and get folder counts for group
      this.getFolderCountsForGroup()

      // load and add media for group
      this.getMedia()

      // toggle upload boxes
      window.backend.mediaLibrary.helper.upload.toggleUploadBoxes()

      // open dialog
      addMediaModal.show()
    })

    // bind change when selecting other folder
    $('#mediaFolders').on('change', (e) => {
      // cache current folder id
      this.config.mediaFolderId = $(e.currentTarget).val()

      // get media for this folder
      this.getMedia()
    })

    $('#searchMedia').on('click', (e) => {
      e.preventDefault();
      e.stopPropagation();

      this.config.mediaFolderId = $('#mediaFolders').val();
      this.config.searchQuery = $('[name=query]').val();

      this.clearMediaCache(mediaFolderId);
      this.getMedia()
    })

    $('[name=query]').bind('keyup input', EventUtil.debounce((e) => {
        this.config.mediaFolderId = $('#mediaFolders').val();
        this.config.searchQuery = $(this).val();

        this.clearMediaCache(mediaFolderId);
        this.getMedia()
      }, 400))
  }

  /**
   * Edit media in a dialog
   */
  editMediaDialog () {
    const $editMediaDialog = $('[data-role=media-library-edit-dialog]')
    const editModalSelector = document.querySelector('[data-role="media-library-edit-dialog"]')
    const editModal = new window.bootstrap.Modal(editModalSelector)
    const $editMediaSubmit = $('#editMediaSubmit')
    const $mediaItemTitleInput = $('#editMediaItemTile');
    let $mediaItem;

    $('[data-fork=connectedItems]').on('click', '[data-fork=edit]', (e) => {
      $mediaItem = $(e.currentTarget).closest('[data-fork=mediaItem]')

      $mediaItemTitleInput.val($mediaItem.data('mediaTitle'))

      editModal.show()

    })

    $mediaItemTitleInput.keyup((e) => {
      if (e.keyCode === 13) {
        $editMediaSubmit.click()
      }
    })

    $editMediaSubmit.click(() => {
      if (!$mediaItem || !$mediaItem.data('mediaId')) {
        return;
      }

      $editMediaDialog.find('.is-invalid').removeClass('is-invalid')
      $editMediaDialog.find('.help-block').remove()

      if ($mediaItemTitleInput.val() === "") {
        $mediaItemTitleInput.addClass('is-invalid');

        $('<span>').addClass('help-block')
          .append(
            $('<ul>').addClass('list-unstyled')
              .append(
                $('<li>').addClass('formError')
                  .html(StringUtil.ucfirst(window.backend.locale.err('FieldIsRequired')))
                  .prepend(
                    $('<span>').addClass('fa fa-exclamation-triangle').attr('aria-hidden', 'true')
                  )
              )
          )
          .insertAfter($mediaItemTitleInput)

        return;
      }

      $.ajax({
        data: {
          fork: {
            module: 'MediaLibrary',
            action: 'MediaItemEditTitle'
          },
          media_id: $mediaItem.data('mediaId'),
          title: $mediaItemTitleInput.val()
        },
        success: (json, textStatus) => {
          if (json.code !== 200) {
            // show error if needed
            if (jsBackend.debug) {
              window.alert(textStatus)
            }

            return
          }

          Messages.add('success', json.message)

          $mediaItem.data('mediaTitle', $mediaItemTitleInput.val())
          $mediaItem.find('img').attr({
            alt: $mediaItemTitleInput.val()
          })

          editModal.hide()
        }
      })
    })

    editModalSelector.addEventListener('hide.bs.modal', () => {
      $mediaItem = null
      $mediaItemTitleInput.val('')
    })
  }

  /**
   * Clear the folders cache when necessary
   */
  clearFoldersCache () {
    this.config.mediaFolders = false
  }

  /**
   * Clear the media cache when necessary
   */
  clearMediaCache (mediaFolderId) {
    media[mediaFolderId] = false
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
    this.config.currentMediaGroupId = groupId

    // current ids
    let currentIds = $.trim($('#group-' + this.config.currentMediaGroupId + ' .mediaIds').first().val()).split(',')

    // remove from array
    currentIds = jQuery.grep(currentIds, (value) => {
      return value !== mediaId
    })

    // redefine current media group
    this.config.currentMediaItemIds = currentIds

    // only get new folder counts on startup
    if (this.config.mediaGroups[this.config.currentMediaGroupId].id !== 0 && this.config.mediaGroups[this.config.currentMediaGroupId].count === undefined) {
      // load folder counts for group using ajax
      $.ajax({
        data: {
          fork: {
            module: 'MediaLibrary',
            action: 'MediaFolderGetCountsForGroup'
          },
          group_id: this.config.mediaGroups[this.config.currentMediaGroupId].id
        },
        success: (json, textStatus) => {
          if (json.code !== 200) {
            // show error if needed
            if (Config.isDebug()) {
              window.alert(textStatus)
            }

            return
          }

          // cache folder counts
          this.config.mediaGroups[this.config.currentMediaGroupId].count = json.data

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
    if (this.config.mediaGroups[this.config.currentMediaGroupId].id !== 0 && this.config.mediaGroups[this.config.currentMediaGroupId].count === undefined) {
      // load folder counts for group using ajax
      $.ajax({
        data: {
          fork: {
            module: 'MediaLibrary',
            action: 'MediaFolderGetCountsForGroup'
          },
          group_id: this.config.mediaGroups[this.config.currentMediaGroupId].id
        },
        success: (json, textStatus) => {
          if (json.code !== 200) {
            // show error if needed
            if (Config.isDebug()) {
              window.alert(textStatus)
            }
          } else {
            // cache folder counts
            this.config.mediaGroups[this.config.currentMediaGroupId].count = json.data

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
    if (this.config.mediaFolders) {
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
          if (Config.isDebug()) {
            window.alert(textStatus)
          }

          return
        }

        // cache folders
        this.config.mediaFolders = json.data

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
      this.config.mediaGroups[mediaGroupId] = {
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
              if (Config.isDebug()) {
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
                window.mediaLibrary.mediaThumbUrl.set(item)
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
    const id = (groupId) || this.config.currentMediaGroupId
    return $('#group-' + id).find('.mediaConnectedItems')
  }

  /**
   * Load in the media for a group or in a folder
   */
  getMedia () {
    // Load media from cache
    if (this.config.mediaFolderId === null || !this.config.media[this.config.mediaFolderId]) {
      this.updateMedia()
    }

    // Load media using ajax
    $.ajax({
      data: {
        fork: {
          module: 'MediaLibrary',
          action: 'MediaItemFindAll'
        },
        group_id: (this.config.mediaGroups[this.config.currentMediaGroupId]) ? this.config.mediaGroups[this.config.currentMediaGroupId].id : null,
        folder_id: this.config.mediaFolderId,
        query: this.config.searchQuery,
        aspect_ratio: this.config.currentAspectRatio
      },
      success: (json, textStatus) => {
        if (json.code !== 200) {
          // show error if needed
          if (Config.isDebug()) {
            window.alert(textStatus)
          }

          return
        }

        // only do this when current folder is different
        if (json.data.folder !== 0) {
          // redefine folder id
          this.config.mediaFolderId = json.data.folder

          // cache media
          this.config.media[this.config.mediaFolderId] = json.data.media
          // redefine current folder as none
        } else {
          this.config.mediaFolderId = 0
        }

        // update media
        this.updateMedia()
      }
    })
  }

  getMediaItemForId (mediaItemId) {
    let foundMediaItem = false

    $.each(this.config.media, (index, mediaFolder) => {
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
    $('#mediaFolders').val(this.config.mediaFolderId)
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
    if (this.config.mediaGroups[this.config.currentMediaGroupId].count === undefined) {
      // define new object
      const obj = {}

      // redefine object
      obj[folderId] = 0

      // add object to count
      this.config.mediaGroups[this.config.currentMediaGroupId].count = obj
    }

    // folder not found - add it to object
    if (this.config.mediaGroups[this.config.currentMediaGroupId].count[folderId] === undefined) {
      // update object to count
      this.config.mediaGroups[this.config.currentMediaGroupId].count[folderId] = 0
    }

    // init count
    let count = parseInt(this.config.mediaGroups[this.config.currentMediaGroupId].count[folderId])

    // subtract or add value
    count = (updateCount === '-') ? (count - updateWithValue) : (count + updateWithValue)

    // redefine count when under zero
    if (count < 0) {
      count = 0
      // redefine amount when max has reached
    } else if (this.config.mediaFolders[folderId] !== undefined && count > this.config.mediaFolders[folderId].numMedia) {
      count = this.config.mediaFolders[folderId].numMedia
    }

    // update count
    this.config.mediaGroups[this.config.currentMediaGroupId].count[folderId] = count

    // update folders
    this.updateFolders()
  }

  updateFolders () {
    // add folders to dropdown
    $('#mediaFolders').html(Templates.getHTMLForMediaFolders(this.config.mediaFolders))

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
    let currentIds = ($('#group-' + this.config.currentMediaGroupId + ' .mediaIds').first().val() !== '')
      ? $.trim($('#group-' + this.config.currentMediaGroupId + ' .mediaIds').first().val()).split(',') : []

    // define empty
    const empty = (this.config.currentMediaItemIds.length === 0)

    // check which items to add
    $(this.config.currentMediaItemIds).each((i, id) => {
      // add item
      if (!currentIds.includes(id)) {
        // loop media folders
        $.each(this.config.media, (index, items) => {
          // loop media items in folder
          $.each(items, (index, item) => {
            // item found
            if (id === item.id) {
              // Add HTML for MediaItem to Connect
              $currentItems.append(Templates.getHTMLForMediaItemToConnect(item))
              window.backend.mediaLibrary.helper.mediaThumbUrl.set(item)
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
    this.config.mediaGroups[this.config.currentMediaGroupId].media = this.config.currentMediaItemIds

    // add empty media paragraph
    if (empty) {
      this.getItems().after('<p class="mediaNoItems helpTxt">' + window.backend.locale.msg('MediaNoItemsConnected') + '</p>')
      // delete empty media paragraph
    } else {
      $('#group-' + this.config.currentMediaGroupId).find('.mediaNoItems').remove()
      $('#group-' + this.config.currentMediaGroupId).find('.media-group-type-errors').remove()
    }

    // update the hidden group field for media
    $('#group-' + this.config.currentMediaGroupId).find('.mediaIds').first().val(this.config.currentMediaItemIds.join(','))

    // redefine
    this.config.currentMediaItemIds = []
  }

  /**
   * Update the media
   */
  updateMedia () {
    // init constiables
    const mediaItemTypes = Data.get('MediaLibrary.mediaItemTypes')
    const html = {}
    const counts = {}
    const rowNoItems = Templates.getHTMLForEmptyTableRow()

    $(mediaItemTypes).each((index, type) => {
      html[type] = ''
      counts[type] = 0
    })

    // loop media
    $.each(this.config.media[this.config.mediaFolderId], (i, item) => {
      // check if media is connected or not
      const connected = (typeof this.config.mediaGroups[this.config.currentMediaGroupId] === 'undefined') ? false : this.config.mediaGroups[this.config.currentMediaGroupId].media.includes(item.id)

      // Redefine
      html[item.type] += Templates.getHTMLForMediaItemTableRow(item, connected)
      counts[item.type] += 1
    })

    $(mediaItemTypes).each((index, type) => {
      const mediaTableHtml = '<thead><tr><th class="check"><span><input type="checkbox" name="toggleChecks" value="toggleChecks" title="Select all"></span></th>' +
        (type === 'image' ? '<th>' + StringUtil.ucfirst(window.backend.locale.lbl('Image')) + '</th>' : '') +
        '<th>' + StringUtil.ucfirst(window.backend.locale.lbl('Filename')) + '</th>' +
        '<th>' + StringUtil.ucfirst(window.backend.locale.lbl('Title')) + '</th>' +
        '</tr></thead>' +
        '<tbody>' + html[type] + '</tbody>'
      $('#mediaTable' + StringUtil.ucfirst(type)).html((html[type]) ? $(mediaTableHtml) : rowNoItems)
      $('#mediaCount' + StringUtil.ucfirst(type)).text('(' + counts[type] + ')')
    })

    // Init toggle for mass-action checkbox
    window.backend.controls.bindMassCheckbox()

    // init $tabs
    const $tabs = $('#tabLibrary').find('.nav-tabs')

    // remove selected
    $tabs.find('.active').removeClass('active')

    // not in connect-to-group modus (just uploading)
    if (typeof this.config.mediaGroups[this.config.currentMediaGroupId] === 'undefined') {
      return false
    }

    // Enable all because we can switch between different groups on the same page
    $tabs.children('.nav-link').removeClass('disabled, active')

    const tabsElement = document.querySelectorAll('#tabLibrary .nav-tabs')[0]

    // we have an image group
    if (this.config.mediaGroups[this.config.currentMediaGroupId].type === 'image') {
      tabsElement.querySelectorAll('.nav-item:not([data-type-image])').forEach((tab) => {
        tab.querySelector('.nav-link').classList.add('disabled')
      })
    } else if (this.config.mediaGroups[this.config.currentMediaGroupId].type === 'file') {
      tabsElement.querySelectorAll('.nav-item:not([data-type-file])').forEach((tab) => {
        tab.querySelector('.nav-link').classList.add('disabled')
      })
    } else if (this.config.mediaGroups[this.config.currentMediaGroupId].type === 'movie') {
      tabsElement.querySelectorAll('.nav-item:not([data-type-movie])').forEach((tab) => {
        tab.querySelector('.nav-link').classList.add('disabled')
      })
    } else if (this.config.mediaGroups[this.config.currentMediaGroupId].type === 'audio') {
      tabsElement.querySelectorAll('.nav-item:not([data-type-audio])').forEach((tab) => {
        tab.querySelector('.nav-link').classList.add('disabled')
      })
    } else if (this.config.mediaGroups[this.config.currentMediaGroupId].type === 'image-file') {
      tabsElement.querySelectorAll('.nav-item:not([data-type-image]):not([data-type-file])').forEach((tab) => {
        tab.querySelector('.nav-link').classList.add('disabled')
      })
    } else if (this.config.mediaGroups[this.config.currentMediaGroupId].type === 'image-movie') {
      tabsElement.querySelectorAll('.nav-item:not([data-type-image]):not([data-type-movie])').forEach((tab) => {
        tab.querySelector('.nav-link').classList.add('disabled')
      })
    }

    const navItem = tabsElement.querySelectorAll('.nav-item:not(.disabled)')[0]
    const navLink = new window.bootstrap.Tab(navItem.querySelectorAll('.nav-link')[0])
    navLink.show()

    // get table
    const $tables = $('.mediaTable')

    // bind change when connecting/disconnecting media
    $tables.find('.toggleConnectedCheckbox').on('change', (e) => {
      // mediaId
      const mediaId = $(e.currentTarget).parent().parent().attr('id').replace('media-', '')

      // Is the checkbox checked or unchecked?
      const isChecked = $(e.currentTarget).is(':checked')

      // was already connected?
      const connected = this.config.currentMediaItemIds.includes(mediaId)

      // delete from array
      if (connected && !isChecked) {
        // loop all to find value and to delete it
        this.config.currentMediaItemIds.splice(this.config.currentMediaItemIds.indexOf(mediaId), 1)

        // update folder count
        this.updateFolderCount(this.config.mediaFolderId, '-', 1)
        // add to array
      } else if (!connected && isChecked) {
        this.config.currentMediaItemIds.push(mediaId)

        // update folder count
        this.updateFolderCount(this.config.mediaFolderId, '+', 1)
      }

      // validate the minimum and maximum count
      this.validateMinimumMaximumCount()
    })

    // bind click to duplicate media item
    $('[data-role=media-library-duplicate-and-crop]').on('click', () => {
      const mediaItemToDuplicate = this.getMediaItemForId($(this).data('media-item-id'))
      /* eslint-disable no-new */
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
    const totalMediaCount = window.backend.mediaLibrary.helper.upload.uploadedCount + this.config.currentMediaItemIds.length
    const $minimumCountError = $('[data-role="fork-media-count-error"]')
    const $submitButton = $('#addMediaSubmit')

    if (this.config.maximumMediaItemsCount !== false && totalMediaCount > this.config.maximumMediaItemsCount) {
      $minimumCountError.html(window.backend.locale.err('MaximumConnectedItems').replace('{{ limit }}', this.config.maximumMediaItemsCount)).removeClass('d-none')
      $submitButton.addClass('disabled').attr('disabled', true)

      return
    }

    if (this.config.minimumMediaItemsCount !== false && totalMediaCount < this.config.minimumMediaItemsCount) {
      $minimumCountError.html(window.backend.locale.err('MinimumConnectedItems').replace('{{ limit }}', this.config.minimumMediaItemsCount)).removeClass('d-none')
      $submitButton.addClass('disabled').attr('disabled', true)

      return
    }

    $minimumCountError.html('').addClass('d-none')
    $submitButton.removeClass('disabled').attr('disabled', false)
  }
}
