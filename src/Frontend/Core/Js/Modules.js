import { Faq } from '../../Modules/Faq/Js/Faq'
import { Location } from '../../Modules/Location/Js/Location'
import { Profiles } from '../../Modules/Profiles/Js/Profiles'

export class Modules {
  initModules () {
    this.faq = new Faq()
    this.location = new Location()
    this.profiles = new Profiles()
  }
}
