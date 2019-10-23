import EditorJS from '@editorjs/editorjs'
import Embed from '@editorjs/embed'
import Header from '@editorjs/header'
import List from '@editorjs/list'
import Paragraph from '@editorjs/paragraph'

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
          console.debug('Saving failed: ', error)
        })
      },
      tools: {
        paragraph: {
          class: Paragraph,
          inlineToolbar: true
        },
        header: {
          class: Header,
          shortcut: 'CMD+SHIFT+H'
        },
        list: {
          class: List,
          inlineToolbar: true
        },
        embed: {
          class: Embed,
          inlineToolbar: true,
          config: {
            services: {
              youtube: true,
              vimeo: true
            }
          }
        }
      }
    })
  }
}

window.BlockEditor = BlockEditor
