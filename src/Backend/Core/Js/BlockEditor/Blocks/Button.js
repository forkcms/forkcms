import { StringUtil } from '../../Components/StringUtil'

class Button {
  constructor ({data}) {
    this.data = {
      url: data.url,
      text: data.text,
      style: data.style,
      targetBlank: data.targetBlank !== undefined ? data.targetBlank : false
    }
    this.settings = [
      {
        name: 'targetBlank',
        icon: `<svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M15.8 10.592v2.043h2.35v2.138H15.8v2.232h-2.25v-2.232h-2.4v-2.138h2.4v-2.28h2.25v.237h1.15-1.15zM1.9 8.455v-3.42c0-1.154.985-2.09 2.2-2.09h4.2v2.137H4.15v3.373H1.9zm0 2.137h2.25v3.325H8.3v2.138H4.1c-1.215 0-2.2-.936-2.2-2.09v-3.373zm15.05-2.137H14.7V5.082h-4.15V2.945h4.2c1.215 0 2.2.936 2.2 2.09v3.42z"/></svg>`,
        title: window.backend.locale.lbl('TargetBlank')
      }
    ]
  }

  static get toolbox () {
    return {
      title: 'Button',
      icon: '<svg width="17" height="15" viewBox="0 0 336 276" xmlns="http://www.w3.org/2000/svg"><path d="M291 150V79c0-19-15-34-34-34H79c-19 0-34 15-34 34v42l67-44 81 72 56-29 42 30zm0 52l-43-30-56 30-81-67-66 39v23c0 19 15 34 34 34h178c17 0 31-13 34-29zM79 0h178c44 0 79 35 79 79v118c0 44-35 79-79 79H79c-44 0-79-35-79-79V79C0 35 35 0 79 0z"/></svg>'
    }
  }

  get CSS () {
    return {
      wrapper: 'ce-wrapper',
      wrapperLabel: 'ce-wrapper-label',
      input: 'ce-input',
      label: 'ce-label',
      spacingBellow: 'mb-2'
    }
  }

  renderSettings () {
    const wrapper = document.createElement('div')

    this.settings.forEach(tune => {
      let button = document.createElement('div')

      button.classList.add('cdx-settings-button')
      if (this.data[tune.name] === true) {
        button.classList.add('cdx-settings-button--active')
      }
      button.innerHTML = tune.icon
      button.title = tune.title
      wrapper.appendChild(button)

      button.addEventListener('click', () => {
        this._toggleTune(tune.name)
        button.classList.toggle('cdx-settings-button--active')
      })
    })

    return wrapper
  }

  _toggleTune (tune) {
    this.data[tune] = !this.data[tune]
  }

  render () {
    // wrapper
    const wrapper = this._make('div', this.CSS.wrapper)
    const wrapperContent = this._make('div')
    const wrapperTitle = this._make('label', this.CSS.wrapperLabel,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Button'))
      }
    )

    // Button text
    const labelText = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Text'))
      }
    )
    const inputText = this._make('input', [this.CSS.input, this.CSS.spacingBellow],
      {
        type: 'text',
        value: this.data && this.data.text ? this.data.text : ''
      },
      {
        'data-input-text': ''
      }
    )

    // Button url
    const labelUrl = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Link'))
      }
    )
    const inputUrl = this._make('input', [this.CSS.input, this.CSS.spacingBellow],
      {
        type: 'text',
        value: this.data && this.data.url ? this.data.url : ''
      },
      {
        'data-input-url': ''
      }
    )

    // Button color
    const labelColor = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Color'))
      }
    )
    const selectColor = this._make('select', this.CSS.input, {}, {
      'data-select-color': ''
    })
    const opt1 = this._make('option', null,
      {
        value: 'primary',
        innerHTML: 'primary'
      }
    )
    const opt2 = this._make('option', null,
      {
        value: 'secondary',
        innerHTML: 'secondary'
      }
    )

    // append to wrapper
    selectColor.appendChild(opt1)
    selectColor.appendChild(opt2)

    wrapperContent.appendChild(labelText)
    wrapperContent.appendChild(inputText)
    wrapperContent.appendChild(labelUrl)
    wrapperContent.appendChild(inputUrl)
    wrapperContent.appendChild(labelColor)
    wrapperContent.appendChild(selectColor)

    wrapper.appendChild(wrapperTitle)
    wrapper.appendChild(wrapperContent)

    return wrapper
  }

  save (blockContent) {
    console.log('SAVE')
    const inputUrl = blockContent.querySelector('[data-input-url]')
    const inputText = blockContent.querySelector('[data-input-text]')
    const selectColor = blockContent.querySelector('[data-select-color]')

    return Object.assign(this.data, {
      url: inputUrl.value,
      text: inputText.value,
      style: selectColor.value
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

export default Button
