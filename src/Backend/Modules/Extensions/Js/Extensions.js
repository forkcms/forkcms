import { Modules } from './Components/Modules'
import { Themes } from './Components/Themes'
import { Template } from './Components/Template'
import { Templates } from './Components/Templates'

export class Extensions {
  constructor () {
    this.modules = new Modules()
    this.themes = new Themes()
    this.template = new Template()
    this.templates = new Templates()
  }
}
