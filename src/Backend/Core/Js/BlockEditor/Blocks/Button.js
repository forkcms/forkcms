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
      title: StringUtil.ucfirst(window.backend.locale.lbl('Button')),
      icon: '<svg width="20" height="10.1" viewBox="0 0 20 10.1" xmlns="http://www.w3.org/2000/svg"><path d="M15.3,2C16.8,2,18,3.2,18,4.7v0.7c0,1.5-1.2,2.7-2.7,2.7H4.7C3.2,8.1,2,6.9,2,5.4V4.7C2,3.2,3.2,2,4.7,2H15.3 M15.3,0H4.7C2.1,0,0,2.1,0,4.7v0.7c0,2.6,2.1,4.7,4.7,4.7h10.5c2.6,0,4.7-2.1,4.7-4.7V4.7C20,2.1,17.9,0,15.3,0L15.3,0z"/><rect x="4.6" y="4.6" width="7.6" height="1"/><polygon points="14.9,6.7 14.2,6 15,5.1 14.2,4.1 15,3.5 16.4,5.1 \t\t"/>\n</svg>'
    }
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
      settingsWrapper: 'cdx-button-settings',
      settingsButton: 'ce-settings__button',
      settingsButtonActive: 'cdx-settings-button--active'
    }
  }

  renderSettings () {
    const wrapper = this._make('div', this.CSS.settingsWrapper)

    this.settings.forEach(tune => {
      let button = this._make('div', this.CSS.settingsButton,
        {
          innerHTML: tune.icon,
          title: tune.title
        })

      if (this.data[tune.name] === true) {
        button.classList.add(this.CSS.settingsButtonActive)
      }

      wrapper.appendChild(button)

      button.addEventListener('click', () => {
        this._toggleTune(tune.name)
        button.classList.toggle(this.CSS.settingsButtonActive)
      })
    })

    return wrapper
  }

  _toggleTune (tune) {
    this.data[tune] = !this.data[tune]
  }

  render () {
    // wrapper
    const wrapper = this._make('div', this.CSS.wrapper, {}, {
      'data-block-content': ''
    })
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
    const inputText = this._make('input', this.CSS.input,
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
    const inputUrl = this._make('input', this.CSS.input,
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
