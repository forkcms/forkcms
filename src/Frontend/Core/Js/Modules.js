import { Faq } from '../../Modules/Faq/Js/Faq'
import { Location } from '../../Modules/Location/Js/Location'

export class Modules {
  initModules () {
    this.faq = new Faq()
    this.location = new Location()
  }
}
