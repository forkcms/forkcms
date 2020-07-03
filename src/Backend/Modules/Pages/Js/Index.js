import 'bootstrap'
import 'jstree/dist/jstree'

import { Tree } from './Components/Tree'
import { Ajax } from '../../../Core/Js/Components/Ajax'
import { Extra } from './Components/Extra'
import { Templates } from './Components/Templates'
import { Move } from './Components/Move'

export class Pages {
  constructor () {
    // main components
    this.ajax = new Ajax()

    // tree components
    this.tree = new Tree()

    // are we adding or editing?
    if ($('[data-role="template-switcher"]').length > 0) {
      // load stuff for editing and adding an page
      this.extra = new Extra()
      this.templates = new Templates()
    }

    if ($('[data-role="move-page-toggle"]').length > 0) {
      this.move = new Move()
    }
  }
}

$(window).on('load', () => {
  new Pages()
})
