import { StringUtil } from '../../../../../../../Core/assets/js/Components/StringUtil'

class TestBlock {
  constructor ({ data }) {
    this.data = {
      text: data.text,
      author: data.author,
      alignment: (Object.values(this.alignments).includes(data.alignment) && data.alignment) || this.defaultAlignment,
      imagePosition: (Object.values(this.imagePositions).includes(data.imagePosition) && data.imagePosition) || this.defaultImagePosition
    }
  }

  static get toolbox () {
    return {
      icon: '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M368 128c0 44.4-25.4 83.5-64 106.4V256c0 17.7-14.3 32-32 32H176c-17.7 0-32-14.3-32-32V234.4c-38.6-23-64-62.1-64-106.4C80 57.3 144.5 0 224 0s144 57.3 144 128zM168 176a32 32 0 1 0 0-64 32 32 0 1 0 0 64zm144-32a32 32 0 1 0 -64 0 32 32 0 1 0 64 0zM3.4 273.7c7.9-15.8 27.1-22.2 42.9-14.3L224 348.2l177.7-88.8c15.8-7.9 35-1.5 42.9 14.3s1.5 35-14.3 42.9L295.6 384l134.8 67.4c15.8 7.9 22.2 27.1 14.3 42.9s-27.1 22.2-42.9 14.3L224 419.8 46.3 508.6c-15.8 7.9-35 1.5-42.9-14.3s-1.5-35 14.3-42.9L152.4 384 17.7 316.6C1.9 308.7-4.5 289.5 3.4 273.7z"/></svg>',
      title: 'Test'
    }
  }

  /**
   * Allowed quote alignments
   *
   * @returns {{left: string, center: string}}
   */
  get alignments () {
    return {
      left: 'left',
      center: 'center'
    }
  }

  get imagePositions () {
    return {
      left: 'left',
      center: 'right'
    }
  }

  /**
   * Default quote alignment
   *
   * @returns {string}
   */
  get defaultAlignment () {
    return this.alignments.left
  }

  get defaultImagePosition () {
    return this.imagePositions.left
  }

  /**
   * CSS classes
   *
   * @returns {{wrapper: string, wrapperLabel: string, input: string, label: string, settingsWrapper: string, settingsButton: string, settingsButtonActive: string}}
   */
  get CSS () {
    return {
      wrapper: 'ce-wrapper',
      wrapperLabel: 'ce-wrapper-label',
      input: 'ce-input',
      label: 'ce-label',
      settingsWrapper: 'cdx-test-settings',
      settingsButton: 'ce-settings__button',
      settingsButtonActive: 'cdx-settings-button--active'
    }
  }

  renderSettings () {
    return [
      {
        icon: '<svg width="16" height="11" viewBox="0 0 16 11" xmlns="http://www.w3.org/2000/svg" ><path d="M1.069 0H13.33a1.069 1.069 0 0 1 0 2.138H1.07a1.069 1.069 0 1 1 0-2.138zm0 4.275H9.03a1.069 1.069 0 1 1 0 2.137H1.07a1.069 1.069 0 1 1 0-2.137zm0 4.275h9.812a1.069 1.069 0 0 1 0 2.137H1.07a1.069 1.069 0 0 1 0-2.137z" /></svg>',
        label: 'Align left',
        toggle: 'alignment', // <--- specify toggle group name
        onActivate: () => {
          this.data.alignment = 'left'
        }
      },
      {
        icon: '<svg width="16" height="11" viewBox="0 0 16 11" xmlns="http://www.w3.org/2000/svg" ><path d="M1.069 0H13.33a1.069 1.069 0 0 1 0 2.138H1.07a1.069 1.069 0 1 1 0-2.138zm3.15 4.275h5.962a1.069 1.069 0 0 1 0 2.137H4.22a1.069 1.069 0 1 1 0-2.137zM1.069 8.55H13.33a1.069 1.069 0 0 1 0 2.137H1.07a1.069 1.069 0 0 1 0-2.137z"/></svg>',
        label: 'Align center',
        toggle: 'alignment', // <--- specify toggle group name
        onActivate: () => {
          this.data.alignment = 'center'
        }
      },
      {
        icon: '<svg width="16" height="11" viewBox="0 0 16 11" xmlns="http://www.w3.org/2000/svg" ><path d="M1.069 0H13.33a1.069 1.069 0 0 1 0 2.138H1.07a1.069 1.069 0 1 1 0-2.138zm0 4.275H9.03a1.069 1.069 0 1 1 0 2.137H1.07a1.069 1.069 0 1 1 0-2.137zm0 4.275h9.812a1.069 1.069 0 0 1 0 2.137H1.07a1.069 1.069 0 0 1 0-2.137z" /></svg>',
        label: 'Image left',
        toggle: 'imagePosition', // <--- specify toggle group name
        onActivate: () => {
          this.data.imagePosition = 'left'
        }
      },
      {
        icon: '<svg width="16" height="11" viewBox="0 0 16 11" xmlns="http://www.w3.org/2000/svg" ><path d="M1.069 0H13.33a1.069 1.069 0 0 1 0 2.138H1.07a1.069 1.069 0 1 1 0-2.138zm3.15 4.275h5.962a1.069 1.069 0 0 1 0 2.137H4.22a1.069 1.069 0 1 1 0-2.137zM1.069 8.55H13.33a1.069 1.069 0 0 1 0 2.137H1.07a1.069 1.069 0 0 1 0-2.137z"/></svg>',
        label: 'Image right',
        toggle: 'imagePosition', // <--- specify toggle group name
        onActivate: () => {
          this.data.imagePosition = 'right'
        }
      }
    ]
  }

  render () {
    // wrapper
    const wrapper = this._make('div', this.CSS.wrapper)
    const wrapperContent = this._make('div')
    const wrapperTitle = this._make('label', this.CSS.wrapperLabel,
      {
        innerHTML: 'Test'
      }
    )

    // Quote text
    const labelText = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Text'))
      }
    )
    const inputText = this._make('textarea', this.CSS.input,
      {
        value: this.data && this.data.text ? this.data.text : '',
        rows: 7
      },
      {
        'data-input-text': ''
      }
    )

    // Quote text
    const labelAuthor = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Author'))
      }
    )
    const inputAuthor = this._make('input', this.CSS.input,
      {
        type: 'text',
        value: this.data && this.data.author ? this.data.author : ''
      },
      {
        'data-input-author': ''
      }
    )
    const imageSelector = this._make('div', 'vue-app',
      {},
      {
        'data-role': 'vue-app'
      }
    )

    // TODO: get image selection from vue app and save it (on event emit)

    const component = this._make('test-component')

    // append to wrapper
    wrapperContent.appendChild(labelText)
    wrapperContent.appendChild(inputText)
    wrapperContent.appendChild(labelAuthor)
    wrapperContent.appendChild(inputAuthor)
    imageSelector.appendChild(component)
    wrapperContent.appendChild(imageSelector)

    wrapper.appendChild(wrapperTitle)
    wrapper.appendChild(wrapperContent)

    return wrapper
  }

  save (blockContent) {
    const inputText = blockContent.querySelector('[data-input-text]')
    const inputAuthor = blockContent.querySelector('[data-input-author]')

    return Object.assign(this.data, {
      text: inputText.value,
      author: inputAuthor.value
    })
  }

  /**
   * Helper for making Elements with attributes
   *
   * @param  {string} tagName           - new Element tag name
   * @param  {Array|string} classNames  - list or name of CSS classname(s)
   * @param  {object} attributes        - any attributes
   * @returns {Element}
   */
  _make (tagName, classNames = null, attributes = {}, customAttributes = {}) {
    const el = document.createElement(tagName)

    if (Array.isArray(classNames)) {
      el.classList.add(...classNames)
    } else if (classNames) {
      el.classList.add(classNames)
    }

    for (const attrName in attributes) {
      el[attrName] = attributes[attrName]
    }

    for (const attrName in customAttributes) {
      el.setAttribute(attrName, customAttributes[attrName])
    }

    return el
  }
}

export default TestBlock
