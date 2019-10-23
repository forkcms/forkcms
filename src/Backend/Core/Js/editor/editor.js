import EditorJS from '@editorjs/editorjs'

class BlockEditor {
  static create ($element) {
    $element.hide()
    let editorId = $element.id + '-block-editor'
    $element.after('<div id="' + editorId + '"></div>')

    const editor = new EditorJS({
      holder: editorId,
      data: JSON.parse($element.text()),
      onChange: () => {
        editor.save().then((outputData) => {
          $element.val(JSON.stringify(outputData))
        }).catch((error) => {
          console.log('Saving failed: ', error)
        })
      }
    })
  }
}

window.BlockEditor = BlockEditor
