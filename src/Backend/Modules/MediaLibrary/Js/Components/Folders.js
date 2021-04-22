import { Messages } from '../../../../Core/Js/Components/Messages'
import { UrlUtil } from '../../../../Core/Js/Components/UrlUtil'
import { Config } from '../../../../Core/Js/Components/Config'
import {StringUtil} from '../../../../Core/Js/Components/StringUtil'

export class Folders {
  constructor () {
    const $addFolderSubmit = $('#addFolderSubmit')
    const $addFolderDialog = $('#addFolderDialog')
    const $folderTitleError = $('#folderTitleError')

    // start or not
    if ($addFolderDialog.length === 0 || $addFolderSubmit.length === 0) {
      return
    }

    // get folder from id
    let selectedFolderId = (UrlUtil.getGetValue('folder')) ? UrlUtil.getGetValue('folder') : ''

    // add folders on startup
    if ($('#uploadMediaFolderId').length > 0 || $('#addFolderParentId').length > 0) {
      this.updateFolders(selectedFolderId)
    }

    $addFolderSubmit.click(() => {
      // hide errors
      $folderTitleError.hide()

      // get selected folder
      selectedFolderId = ($('#uploadMediaFolderId').val()) ? $('#uploadMediaFolderId').val() : selectedFolderId

      // update folders
      this.updateFolders(selectedFolderId, true)

      $.ajax({
        data: {
          fork: {module: 'MediaLibrary', action: 'MediaFolderAdd'},
          name: $('#addFolderTitle').val(),
          parent_id: $('#addFolderParentId').val()
        },
        success: (json, textStatus) => {
          if (json.code !== 200) {
            // show error if needed
            if (Config.isDebug()) {
              window.alert(textStatus)
            }

            return
          }

          // add and set selected
          $('#mediaConnectedId').append('<option value="' + json.data.id + '">' + json.data.name + '</option>')

          // show message
          Messages.add('success', window.backend.locale.msg('FolderIsAdded'))

          // update folders
          this.updateFolders(json.data.id)

          $addFolderDialog.modal('hide')
        }
      })
    })

    // bind click
    $('#addFolder').on('click', (e) => {
      // prevent default
      e.preventDefault()

      // open dialog
      $addFolderDialog.modal('show')

      // Focus the text field
      $('#addFolderTitle').focus()
    })

    $addFolderDialog.on('hide.bs.modal', () => {
      $('#addFolderTitle').val('')
    })
  }

  /**
   * Get and update the folders using ajax
   *
   * @param {int} selectFolderId [optional] - Selects this folder
   */
  updateFolders (selectFolderId, dialog) {
    // define select folder id
    selectFolderId = (selectFolderId !== null) ? selectFolderId : false
    dialog = !!dialog

    // get folders using ajax
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

          // show message
          $('#addFolderTitle').show()

          return
        }

        const html = this.getHTMLForMediaFolders(json.data)

        // update folders in media module
        if (!dialog) {
          // add folders to dropdowns
          $('#mediaFolders, #uploadMediaFolderId, #addFolderParentId').html(html)

          // select the new folder
          if (selectFolderId) {
            $('#uploadMediaFolderId').val(selectFolderId)
          } else {
            $('#uploadMediaFolderId option:eq(0)').attr('selected', 'selected')
          }

          // update boxes
          if (typeof window.backend.mediaLibrary.helper !== 'undefined') {
            window.backend.mediaLibrary.helper.upload.toggleUploadBoxes()
          }
        }

        // add folders to dropdown
        $('#addFolderParentId').html('<option value="0">' + window.backend.locale.lbl('MediaFolderRoot') + '</option>' + html)

        // select the new folder
        if (selectFolderId) {
          $('#addFolderParentId').val(selectFolderId)
        }
      }
    })
  }

  /**
   * Get HTML for MediaFolders to show in dropdown
   *
   * @param {array} mediaFolders The mediaFolderCacheItem entity array.
   * @returns {string}
   */
  getHTMLForMediaFolders (mediaFolders) {
    var html = ''

    $(mediaFolders).each((i, mediaFolder) => {
      html += this.getHTMLForMediaFolder(mediaFolder)
    })

    return html
  }

  /**
   * Get HTML for MediaFolder to show in dropdown
   *
   * @param {array} mediaFolder The mediaFolderCacheItem entity array.
   * @returns {string}
   */
  getHTMLForMediaFolder (mediaFolder) {
    var html = '<option value="' + mediaFolder.id + '">' + StringUtil.htmlEncode(mediaFolder.slug) + '</option>'

    if (mediaFolder.numberOfChildren > 0) {
      html += this.getHTMLForMediaFolders(mediaFolder.children)
    }

    return html
  }
}
