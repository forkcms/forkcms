import { Group } from './Helper/Group'
import { ModalSelection } from './Helper/ModalSelection'
import { Upload } from './Helper/Upload'
import { Templates } from './Helper/Templates'
import { Data } from '../../../Core/Js/Components/Data'

export class MediaLibraryHelper {
  constructor () {
    // main variables
    this.media = {}
    this.mediaFolders = false
    this.mediaGroups = {}
    this.currentMediaGroupId = 0
    this.mediaFolderId = null
    this.currentAspectRatio = false
    this.minimumMediaItemsCount = false
    this.maximumMediaItemsCount = false
    this.currentMediaItemIds = []

    // init
    this.buildMovieStorageTypeDropdown()
    this.group = new Group()
    this.upload = new Upload()
    this.templates = new Templates()
    this.modalSelection = new ModalSelection()
  }

  buildMovieStorageTypeDropdown () {
    // Add movie storage type in MediaLibraryHelper
    const $movieStorageTypeDropdown = $('#mediaMovieStorageType')
    $(Data.get('MediaLibrary.mediaAllowedMovieSource')).each((index, value) => {
      let html = '<option value="' + value + '"'

      if (index === 0) {
        html += ' selected="selected"'
      }

      html += '>' + value + '</option>'

      // Add to dropdown
      $movieStorageTypeDropdown.append(html)
    })
  }

}
