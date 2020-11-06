import { Messages } from '../../../../Core/Js/Components/Messages'
import { Config } from '../../../../Core/Js/Components/Config'

export class Library {
  constructor () {
    this.currentType = null

    // start or not
    if ($('#library').length === 0) {
      return false
    }

    // init edit folder dialog
    this.addEditFolderDialog()

    // init mass action hidden input fields
    this.dataGrids()
  }

  /**
   * Add edit folder dialog
   */
  addEditFolderDialog () {
    const $editMediaFolderDialog = $('#editMediaFolderDialog')
    const $editMediaFolderSubmit = $('#editMediaFolderSubmit')

    // stop here
    if ($editMediaFolderDialog.length === 0) {
      return false
    }

    $editMediaFolderSubmit.on('click', () => {
      // Update folder using ajax
      $.ajax({
        data: {
          fork: {action: 'MediaFolderEdit'},
          folder_id: $('#mediaFolderId').val(),
          name: $('#mediaFolderName').val()
        },
        success: (json, textStatus) => {
          if (json.code !== 200) {
            // show error if needed
            if (Config.isDebug()) {
              window.alert(textStatus)
            }

            // show message
            Messages.add('error', textStatus)

            return
          }

          // show message
          Messages.add('success', json.message)

          // close dialog
          $('#editFolderDialog').modal('close')

          // reload document
          window.location.reload(true)
        }
      })
    })
  }

  /**
   * Move audio to another folder or connect audio to a gallery
   */
  dataGrids () {
    if (window.location.hash === '') {
      // select first tab
      $('#library .nav-tabs .nav-item:first .nav-link').tab('show')
    }

    // When mass action button is clicked
    $('.jsMassActionSubmit').on('click', (event) => {
      // We remember the current type (image, file, movie, audio, ...)
      this.currentType = $(event.currentTarget).parent().find('select[name=action]').attr('id').replace('mass-action-', '')
    })

    // Submit form
    $('#confirmMassActionMediaItemMove').find('button[type=submit]').on('click', () => {
      $('#move-to-folder-id-for-type-' + this.currentType).val($('#moveToFolderId').val())
      $('#form-for-' + this.currentType).submit()
    })

    $('#confirmMassActionMediaItemDelete').find('button[type=submit]').on('click', () => {
      $('#form-for-' + this.currentType).submit()
    })
  }
}
