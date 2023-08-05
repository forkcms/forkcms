import EditorJS from '@editorjs/editorjs'
import Embed from '@editorjs/embed'
import Header from '@editorjs/header'
import List from '@editorjs/list'
import Paragraph from '@editorjs/paragraph'
import Underline from '@editorjs/underline'
import MediaLibraryImage from './Blocks/MediaLibraryImage'
import MediaLibraryVideo from './Blocks/MediaLibraryVideo'
import Button from './Blocks/Button'
import Quote from './Blocks/Quote'
import Raw from './Blocks/Raw'

export class BlockEditor {
  constructor () {
    this.initEditors($('textarea.inputBlockEditor'))
    this.loadEditorsInCollections()
  }

  initEditors (editors) {
    if (editors.length > 0) {
      editors.each((index, editor) => {
        this.createEditor($(editor))
      })
    }
  }

  createEditor ($element) {
    BlockEditor.fromJson($element, $element.attr('fork-block-editor-config'))
  }

  loadEditorsInCollections () {
    $('[data-addfield="collection"]').on('collection-field-added', (event, formCollectionItem) => {
      this.initEditors($(formCollectionItem).find('textarea.inputBlockEditor'))
    })
  }

  static getClassFromVariableName (string) {
    let scope = window
    let scopeSplit = string.split('.')
    let i

    for (i = 0; i < scopeSplit.length - 1; i++) {
      scope = scope[scopeSplit[i]]

      if (scope === undefined) return
    }

    return scope[scopeSplit[scopeSplit.length - 1]]
  }

  static fromJson ($element, jsonConfig) {
    let config = JSON.parse(jsonConfig)
    for (const name of Object.keys(config)) {
      config[name].class = BlockEditor.getClassFromVariableName(config[name].class)
    }

    BlockEditor.create($element, config)
  }

  static create ($element, tools) {
    $element.hide()
    let editorId = $element.attr('id') + '-block-editor'
    $element.after('<div id="' + editorId + '"></div>')

    let data = {}
    try {
      data = JSON.parse($element.text())
    } catch (e) {
      // ignore the current content since we can't decode it
    }

    const editor = new EditorJS({
      holder: editorId,
      data: data,
      onChange: () => {
        editor.save().then((outputData) => {
          $element.val(JSON.stringify(outputData))
        }).catch((error) => {
          console.debug('Saving failed: ', error)
        })
      },
      tools: tools
    })
  }
}

if (window.BlockEditor === undefined) {
  window.BlockEditor = {blocks: {}}
}

if (window.BlockEditor.blocks === undefined) {
  window.BlockEditor.blocks = {}
}

window.BlockEditor.editor = BlockEditor
window.BlockEditor.blocks.Header = Header
window.BlockEditor.blocks.Embed = Embed
window.BlockEditor.blocks.List = List
window.BlockEditor.blocks.Paragraph = Paragraph
window.BlockEditor.blocks.Underline = Underline
window.BlockEditor.blocks.MediaLibraryImage = MediaLibraryImage
window.BlockEditor.blocks.MediaLibraryVideo = MediaLibraryVideo
window.BlockEditor.blocks.Button = Button
window.BlockEditor.blocks.Quote = Quote
window.BlockEditor.blocks.Raw = Raw
