import { InlineTextEdit } from '../../../Core/Js/Components/InlineTextEdit'

export class Tags {
  constructor () {
    const $dataGridTag = $('.jsDataGrid td.tag')

    if ($dataGridTag.length > 0) {
      const options = { params: { fork: { action: 'edit' } }, tooltip: window.backend.locale.msg('ClickToEdit') }
      InlineTextEdit.inlineTextEdit(options, $dataGridTag)
    }
  }
}
