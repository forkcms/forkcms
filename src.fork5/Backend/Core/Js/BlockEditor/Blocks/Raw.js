import { StringUtil } from '../../Components/StringUtil'

class Raw {
  constructor ({data}) {
    this.data = {
      html: data.html || ''
    }
  }

  static get toolbox () {
    return {
      icon: '<svg width="19" height="13"><path d="M18.004 5.794c.24.422.18.968-.18 1.328l-4.943 4.943a1.105 1.105 0 1 1-1.562-1.562l4.162-4.162-4.103-4.103A1.125 1.125 0 1 1 12.97.648l4.796 4.796c.104.104.184.223.239.35zm-15.142.547l4.162 4.162a1.105 1.105 0 1 1-1.562 1.562L.519 7.122c-.36-.36-.42-.906-.18-1.328a1.13 1.13 0 0 1 .239-.35L5.374.647a1.125 1.125 0 0 1 1.591 1.591L2.862 6.341z"/></svg>',
      title: 'Raw HTML'
    }
  }

  /**
   * CSS classes
   *
   * @returns {{wrapper: string, wrapperLabel: string, input: string, label: string}}
   */
  get CSS () {
    return {
      wrapper: 'ce-wrapper',
      wrapperLabel: 'ce-wrapper-label',
      input: 'ce-input',
      label: 'ce-label'
    }
  }

  render () {
    // wrapper
    const wrapper = this._make('div', this.CSS.wrapper)
    const wrapperContent = this._make('div')
    const wrapperTitle = this._make('label', this.CSS.wrapperLabel,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('EmbedIframe'))
      }
    )

    // Button text
    const labelRaw = this._make('label', this.CSS.label,
      {
        innerHTML: StringUtil.ucfirst(window.backend.locale.lbl('Text'))
      }
    )
    const inputRaw = this._make('textarea', this.CSS.input,
      {
        value: this.data && this.data.html ? this.data.html : '',
        rows: 7
      },
      {
        'data-textarea-html': ''
      }
    )

    // append to wrapper
    wrapperContent.appendChild(labelRaw)
    wrapperContent.appendChild(inputRaw)

    wrapper.appendChild(wrapperTitle)
    wrapper.appendChild(wrapperContent)

    return wrapper
  }

  save (blockContent) {
    const inputRaw = blockContent.querySelector('[data-textarea-html]')

    return Object.assign(this.data, {
      html: inputRaw.value
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

  /**
   * Automatic sanitize config
   */
  static get sanitize () {
    return {
      html: true // Allow HTML tags
    }
  }
}

export default Raw
