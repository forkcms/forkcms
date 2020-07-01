import { Library } from './Components/Library'
import { Controls } from './Components/Controls'
import { Tree } from './Components/Tree'
import { Folders } from './Components/Folders'
import { MediaLibraryHelper } from './MediaLibraryHelper'

export class MediaLibrary {
  constructor () {
    this.controls = new Controls()
    this.library = new Library()
    this.tree = new Tree()
    this.folders = new Folders()
    this.helper = new MediaLibraryHelper()
  }
}

$(window).on('load', () => {
  new MediaLibrary()
})
