import 'bootstrap-tagsinput/examples/lib/typeahead.js/dist/typeahead.bundle'
import 'bootstrap-tagsinput/dist/bootstrap-tagsinput.min'

import { Faq } from '../../Modules/Faq/Js/Faq'
import { Location } from '../../Modules/Location/Js/Location'
import { Profiles } from '../../Modules/Profiles/Js/Profiles'
import { Search } from '../../Modules/Search/Js/Search'
import { MediaLibrary } from '../../Modules/MediaLibrary/Js/MediaLibrary'

export class Modules {
  initModules () {
    this.faq = new Faq()
    this.location = new Location()
    this.profiles = new Profiles()
    this.search = new Search()
    this.mediaLibrary = new MediaLibrary()
  }
}
