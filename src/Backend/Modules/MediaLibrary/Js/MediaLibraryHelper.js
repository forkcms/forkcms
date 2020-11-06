import { Group } from './Helper/Group'
import { ModalSelection } from './Helper/ModalSelection'
import { Upload } from './Helper/Upload'
import { Data } from '../../../Core/Js/Components/Data'
import { Cropper } from './Helper/Cropper'
import { MovieThumbUrl } from './Helper/MovieThumbUrl'

export class MediaLibraryHelper {
  constructor () {
    // main variables
    this.config = {
      media: {},
      mediaFolders: false,
      mediaGroups: {},
      currentMediaGroupId: 0,
      mediaFolderId: null,
      currentAspectRatio: false,
      minimumMediaItemsCount: false,
      maximumMediaItemsCount: false,
      currentMediaItemIds: []
    }

    // init
    this.buildMovieStorageTypeDropdown()
    this.group = new Group(this.config)
    this.upload = new Upload(this.config)
    this.modalSelection = new ModalSelection(this.config)
    this.cropper = new Cropper(this.config)
    this.mediaThumbUrl = new MovieThumbUrl()
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
