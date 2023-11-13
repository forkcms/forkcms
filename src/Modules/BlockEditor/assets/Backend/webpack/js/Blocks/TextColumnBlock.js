import { StringUtil } from '../../../../../../../Core/assets/js/Components/StringUtil'

class TextColumnBlock {
  constructor ({ data, block }) {
    this.block = block
    this.data = {
      title1: data.title1,
      text1: data.text1,
      title2: data.title2,
      text2: data.text2
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
      title: 'Text-Text'
    }
  }

  static get enableLineBreaks () {
    return true
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
      inputText: 'ce-input-text',
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

  render () {
    // wrapper
    const wrapper = this._make('div', this.CSS.wrapper)
    // wrapper.classList.add(this.CSS.alignment[this.data.alignment])
    const wrapperContent = this._make('div')
    const wrapperTitle = this._make('label', this.CSS.wrapperLabel,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('TextColumns'))
      }
    )
    const rowEl = this._make('div', 'row')
    const colOneEl = this._make('div', 'col')
    const colTwoEl = this._make('div', 'col')

    // Block title 1
    const label1Title = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Title'))
      }
    )

    const input1Title = this._make('div', this.CSS.input,
      {
      },
      {
        'data-input-title-one': '',
        contenteditable: 'true'
      }
    )

    input1Title.textContent = this.data && this.data.title1 ? this.data.title1 : ''

    // Block title 1
    const label2Title = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Title'))
      }
    )

    const input2Title = this._make('div', this.CSS.input,
      {
      },
      {
        'data-input-title-two': '',
        contenteditable: 'true'
      }
    )

    input2Title.textContent = this.data && this.data.title2 ? this.data.title2 : ''

    // Block text
    const label1Text = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Text'))
      }
    )

    const input1Text = this._make('div', [this.CSS.input, this.CSS.inputText],
      {
      },
      {
        'data-input-text-one': '',
        contenteditable: 'true'
      }
    )

    input1Text.textContent = this.data && this.data.text1 ? this.data.text1 : ''

    // Block text
    const label2Text = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Text'))
      }
    )

    const input2Text = this._make('div', [this.CSS.input, this.CSS.inputText],
      {
      },
      {
        'data-input-text-two': '',
        contenteditable: 'true'
      }
    )

    input2Text.textContent = this.data && this.data.text2 ? this.data.text2 : ''

    // append to wrapper
    colOneEl.appendChild(label1Title)
    colOneEl.appendChild(input1Title)
    colOneEl.appendChild(label1Text)
    colOneEl.appendChild(input1Text)
    colTwoEl.appendChild(label2Title)
    colTwoEl.appendChild(input2Title)
    colTwoEl.appendChild(label2Text)
    colTwoEl.appendChild(input2Text)
    rowEl.appendChild(colOneEl)
    rowEl.appendChild(colTwoEl)

    wrapper.appendChild(wrapperTitle)
    wrapper.appendChild(wrapperContent)
    wrapperContent.appendChild(rowEl)

    return wrapper
  }

  save (blockContent) {
    const inputTitle1 = blockContent.querySelector('[data-input-title-one]')
    const inputText1 = blockContent.querySelector('[data-input-text-one]')
    const inputTitle2 = blockContent.querySelector('[data-input-title-two]')
    const inputText2 = blockContent.querySelector('[data-input-text-two]')

    return Object.assign(this.data, {
      title1: inputTitle1.innerText,
      text1: inputText1.innerText,
      title2: inputTitle2.innerText,
      text2: inputText2.innerText
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

export default TextColumnBlock
