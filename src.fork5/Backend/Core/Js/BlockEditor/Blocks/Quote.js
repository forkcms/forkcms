import { StringUtil } from '../../Components/StringUtil'

class Quote {
  constructor ({data}) {
    this.data = {
      text: data.text,
      author: data.author,
      alignment: (Object.values(this.alignments).includes(data.alignment) && data.alignment) || this.defaultAlignment
    }
    this.settings = [
      {
        name: 'left',
        icon: `<svg width="16" height="11" viewBox="0 0 16 11" xmlns="http://www.w3.org/2000/svg" ><path d="M1.069 0H13.33a1.069 1.069 0 0 1 0 2.138H1.07a1.069 1.069 0 1 1 0-2.138zm0 4.275H9.03a1.069 1.069 0 1 1 0 2.137H1.07a1.069 1.069 0 1 1 0-2.137zm0 4.275h9.812a1.069 1.069 0 0 1 0 2.137H1.07a1.069 1.069 0 0 1 0-2.137z" /></svg>`
      },
      {
        name: 'center',
        icon: `<svg width="16" height="11" viewBox="0 0 16 11" xmlns="http://www.w3.org/2000/svg" ><path d="M1.069 0H13.33a1.069 1.069 0 0 1 0 2.138H1.07a1.069 1.069 0 1 1 0-2.138zm3.15 4.275h5.962a1.069 1.069 0 0 1 0 2.137H4.22a1.069 1.069 0 1 1 0-2.137zM1.069 8.55H13.33a1.069 1.069 0 0 1 0 2.137H1.07a1.069 1.069 0 0 1 0-2.137z"/></svg>`
      }
    ]
  }

  static get toolbox () {
    return {
      icon: '<svg width="15" height="14" viewBox="0 0 15 14" xmlns="http://www.w3.org/2000/svg"><path d="M13.53 6.185l.027.025a1.109 1.109 0 0 1 0 1.568l-5.644 5.644a1.109 1.109 0 1 1-1.569-1.568l4.838-4.837L6.396 2.23A1.125 1.125 0 1 1 7.986.64l5.52 5.518.025.027zm-5.815 0l.026.025a1.109 1.109 0 0 1 0 1.568l-5.644 5.644a1.109 1.109 0 1 1-1.568-1.568l4.837-4.837L.58 2.23A1.125 1.125 0 0 1 2.171.64L7.69 6.158l.025.027z" /></svg>',
      title: 'Quote'
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

  /**
   * Default quote alignment
   *
   * @returns {string}
   */
  get defaultAlignment () {
    return this.alignments.left
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
      settingsWrapper: 'cdx-quote-settings',
      settingsButton: 'ce-settings__button',
      settingsButtonActive: 'cdx-settings-button--active'
    }
  }

  renderSettings () {
    const wrapper = this._make('div')

    this.settings
      .map(tune => {
        const el = this._make('div', this.CSS.settingsButton, {
          innerHTML: tune.icon,
          title: `${tune.name} alignment`
        })

        el.classList.toggle(this.CSS.settingsButtonActive, tune.name === this.data.alignment)

        wrapper.appendChild(el)

        return el
      })
      .forEach((element, index, elements) => {
        element.addEventListener('click', () => {
          this._toggleTune(this.settings[index].name)

          elements.forEach((el, i) => {
            const { name } = this.settings[i]

            el.classList.toggle(this.CSS.settingsButtonActive, name === this.data.alignment)
          })
        })
      })

    return wrapper
  }

  /**
   * Toggle quote`s alignment
   *
   * @param {string} tune - alignment
   * @private
   */
  _toggleTune (tune) {
    this.data.alignment = tune
  }

  render () {
    // wrapper
    const wrapper = this._make('div', this.CSS.wrapper)
    const wrapperContent = this._make('div')
    const wrapperTitle = this._make('label', this.CSS.wrapperLabel,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Quote'))
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

    // append to wrapper
    wrapperContent.appendChild(labelText)
    wrapperContent.appendChild(inputText)
    wrapperContent.appendChild(labelAuthor)
    wrapperContent.appendChild(inputAuthor)

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

export default Quote
