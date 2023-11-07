import EditorJS from '@editorjs/editorjs'
import Embed from '@editorjs/embed'
import Header from '@editorjs/header'
import List from '@editorjs/list'
import Paragraph from '@editorjs/paragraph'
import Underline from '@editorjs/underline'
import Button from './Blocks/Button'
import Quote from './Blocks/Quote'
import Raw from './Blocks/Raw'
import TestBlock from './Blocks/TestBlock'
import { createApp } from 'vue'
import TestComponent from '../../../../../Backend/assets/Backend/webpack/js/Components/TestComponent.vue'
const AlignmentTuneTool = require('editorjs-text-alignment-blocktune')

export class BlockEditor {
  constructor () {
    this.initEditors($('textarea[data-fork-block-editor-config]'))
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
    BlockEditor.fromJson($element, $element.attr('data-fork-block-editor-config'))
  }

  loadEditorsInCollections () {
    $('[data-addfield="collection"]').on('collection-field-added', (event, formCollectionItem) => {
      this.initEditors($(formCollectionItem).find('textarea[data-fork-block-editor-config]'))
    })
  }

  static getClassFromVariableName (string) {
    let scope = window
    const scopeSplit = string.split('.')
    let i

    for (i = 0; i < scopeSplit.length - 1; i++) {
      scope = scope[scopeSplit[i]]

      if (scope === undefined) return
    }

    return scope[scopeSplit[scopeSplit.length - 1]]
  }

  static fromJson ($element, jsonConfig) {
    const config = JSON.parse(jsonConfig)
    console.log(config)
    for (const name of Object.keys(config)) {
      console.log(config[name])
      config[name].class = BlockEditor.getClassFromVariableName(config[name].class)
    }

    BlockEditor.create($element, config)
  }

  static create ($element, tools) {
    $element.hide()
    const editorId = $element.attr('id') + '-block-editor'
    $element.after('<div id="' + editorId + '"></div>')
    tools.AlignmentBlockTune = {
      class: AlignmentTuneTool,
      config: {
        default: 'left'
      }
    }

    let data = {}
    try {
      data = JSON.parse($element.text())
    } catch (e) {
      // ignore the current content since we can't decode it
    }

    const editor = new EditorJS({
      holder: editorId,
      inlineToolbar: true,
      data,
      onReady: () => {
        // initialize vue app to enable media library image selector in editor js
        const vueApp = document.querySelector('[data-role="vue-app"]')
        if (vueApp) {
          const app = createApp()
          // global component
          app.component('TestComponent', TestComponent)
          app.mount('.vue-app')
        }
      },
      onChange: () => {
        editor.save().then((outputData) => {
          $element.val(JSON.stringify(outputData))
        }).catch((error) => {
          console.debug('Saving failed: ', error)
        })
      },
      tools
    })
  }
}

$(window).on('load', () => {
  if (window.BlockEditor === undefined) {
    window.BlockEditor = { blocks: {} }
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
  window.BlockEditor.blocks.Button = Button
  window.BlockEditor.blocks.Quote = Quote
  window.BlockEditor.blocks.Raw = Raw
  window.BlockEditor.blocks.TestBlock = TestBlock

  window.backend.blockEditor = new BlockEditor()
})
