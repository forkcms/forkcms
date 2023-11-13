import { StringUtil } from '../../../../../../../Core/assets/js/Components/StringUtil'

class TextImageBlock {
  constructor ({ data, block }) {
    this.block = block
    this.data = {
      title: data.title,
      text: data.text,
      buttonText: data.buttonText,
      buttonUrl: data.buttonUrl,
      imagePosition: (Object.values(this.imagePositions).includes(data.imagePosition) && data.imagePosition) || this.defaultImagePosition
    }

    console.log('data block: ', data)
    console.log('block: ', block)
  }

  getBlockHolder () {
    return this.block.holder
  }

  static get toolbox () {
    return {
      icon: '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 512 512"><!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. --><path d="M0 96C0 60.7 28.7 32 64 32H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm64 64V416H224V160H64zm384 0H288V416H448V160z"/></svg>',
      title: 'Text-Image'
    }
  }

  static get enableLineBreaks () {
    return true
  }

  /**
   * Allowed quote alignments
   *
   * @returns {{left: string, center: string}}
   */
  get alignments () {
    return {
      left: 'left',
      center: 'center',
      right: 'right'
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
      settingsButtonActive: 'cdx-settings-button--active',
      alignment: {
        left: 'ce-tune-alignment--left',
        center: 'ce-tune-alignment--center',
        right: 'ce-tune-alignment--right'
      }
    }
  }

  renderSettings () {
    return [
      {
        icon: '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M288 64c0 17.7-14.3 32-32 32H32C14.3 96 0 81.7 0 64S14.3 32 32 32H256c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H256c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>',
        label: 'Image left',
        toggle: 'image', // <--- specify toggle group name
        onActivate: () => {
          this.data.imagePosition = 'left'
          this.block.dispatchChange()
          console.log(this.data.imagePosition)
        },
        isActive: this.data.imagePosition === 'left'
      },
      {
        icon: '<svg xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"><path d="M448 64c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zm0 256c0 17.7-14.3 32-32 32H192c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32zM0 192c0-17.7 14.3-32 32-32H416c17.7 0 32 14.3 32 32s-14.3 32-32 32H32c-17.7 0-32-14.3-32-32zM448 448c0 17.7-14.3 32-32 32H32c-17.7 0-32-14.3-32-32s14.3-32 32-32H416c17.7 0 32 14.3 32 32z"/></svg>',
        label: 'Image right',
        toggle: 'image', // <--- specify toggle group name
        onActivate: () => {
          this.data.imagePosition = 'right'
          this.block.dispatchChange()
          console.log(this.data.imagePosition)
        },
        isActive: this.data.imagePosition === 'right'
      }
    ]
  }

  render () {
    // wrapper
    const wrapper = this._make('div', this.CSS.wrapper)
    // wrapper.classList.add(this.CSS.alignment[this.data.alignment])
    const wrapperContent = this._make('div')
    const wrapperTitle = this._make('label', this.CSS.wrapperLabel,
      {
        innerHTML: 'Test'
      }
    )

    // Block title
    const labelTitle = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Title'))
      }
    )

    const inputTitle = this._make('div', this.CSS.input,
      {
      },
      {
        'data-input-title': '',
        contenteditable: 'true'
      }
    )

    inputTitle.textContent = this.data && this.data.title ? this.data.title : ''

    // Block text
    const labelText = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Text'))
      }
    )

    const inputText = this._make('div', this.CSS.input,
      {
      },
      {
        'data-input-text': '',
        contenteditable: 'true'
      }
    )

    inputText.textContent = this.data && this.data.text ? this.data.text : ''

    // Button text
    const labelButtonText = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('ButtonText'))
      }
    )
    const inputButtonText = this._make('input', this.CSS.input,
      {
        type: 'text',
        value: this.data && this.data.buttonText ? this.data.buttonText : ''
      },
      {
        'data-input-button-text': ''
      }
    )

    // Button url
    const labelButtonUrl = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Link'))
      }
    )
    const inputButtonUrl = this._make('input', this.CSS.input,
      {
        type: 'text',
        value: this.data && this.data.buttonUrl ? this.data.buttonUrl : ''
      },
      {
        'data-input-button-url': ''
      }
    )

    // Block image
    const imageSelector = this._make('div', 'vue-app',
      {},
      {
        'data-role': 'vue-app'
      }
    )

    // TODO: get image selection from vue app and save it (on event emit)

    const component = this._make('test-component')

    // append to wrapper
    wrapperContent.appendChild(labelTitle)
    wrapperContent.appendChild(inputTitle)
    wrapperContent.appendChild(labelText)
    wrapperContent.appendChild(inputText)
    wrapperContent.appendChild(labelButtonUrl)
    wrapperContent.appendChild(inputButtonUrl)
    wrapperContent.appendChild(labelButtonText)
    wrapperContent.appendChild(inputButtonText)
    imageSelector.appendChild(component)
    wrapperContent.appendChild(imageSelector)

    wrapper.appendChild(wrapperTitle)
    wrapper.appendChild(wrapperContent)

    return wrapper
  }

  save (blockContent) {
    const inputTitle = blockContent.querySelector('[data-input-title]')
    const inputText = blockContent.querySelector('[data-input-text]')
    const inputButtonText = blockContent.querySelector('[data-input-button-text]')
    const inputButtonUrl = blockContent.querySelector('[data-input-button-url]')

    return Object.assign(this.data, {
      title: inputTitle.innerText,
      text: inputText.innerText,
      buttonText: inputButtonText.value,
      buttonUrl: inputButtonUrl.value
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

export default TextImageBlock
