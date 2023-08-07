import 'jstree/dist/jstree'

import { Tree } from './Components/Tree'
import { Move } from './Components/Move'
import { Block } from './Components/Block'
import { Templates } from './Components/Templates'

class Pages {
  constructor () {
    // tree components
    this.tree = new Tree()

    if ($('[data-role="move-page-toggle"]').length > 0) {
      this.move = new Move()
    }
    if ($('[data-role="template-switcher"]').length > 0) {
      this.block = new Block()
      this.templates = new Templates()
    }
  }
}

$(function () {
  new Pages()
})
