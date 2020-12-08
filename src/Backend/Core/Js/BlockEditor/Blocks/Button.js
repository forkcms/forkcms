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
    this.wrapper = document.createElement('div')
    this.inputTextWrapper = document.createElement('div')
    this.inputUrlWrapper = document.createElement('div')
    this.selectColorWrapper = document.createElement('div')

    this.wrapper.classList.add('button-wrapper')
    this.inputTextWrapper.classList.add('form-group')
    this.inputUrlWrapper.classList.add('form-group')
    this.selectColorWrapper.classList.add('form-group')

    const inputUrl = document.createElement('input')
    const inputText = document.createElement('input')
    const selectColor = document.createElement('select')

    this.inputTextWrapper.appendChild(inputText)
    this.inputUrlWrapper.appendChild(inputUrl)
    this.selectColorWrapper.appendChild(selectColor)

    this.wrapper.appendChild(this.inputUrlWrapper)
    this.wrapper.appendChild(this.inputTextWrapper)
    this.wrapper.appendChild(this.selectColorWrapper)

    inputUrl.placeholder = 'Button url here'
    inputUrl.value = this.data && this.data.url ? this.data.url : ''
    inputUrl.setAttribute('data-input-url', null)
    inputUrl.classList.add('form-control')

    inputText.placeholder = 'Button text here'
    inputText.value = this.data && this.data.text ? this.data.text : ''
    inputText.setAttribute('data-input-text', null)
    inputText.classList.add('form-control')

    selectColor.setAttribute('data-select-color', null)
    selectColor.classList.add('form-control')

    const opt1 = document.createElement('option')
    const opt2 = document.createElement('option')
    selectColor.appendChild(opt1)
    selectColor.appendChild(opt2)

    opt1.appendChild(document.createTextNode('primary'))
    opt2.appendChild(document.createTextNode('secondary'))

    opt1.value = 'primary'
    opt2.value = 'secondary'

    return this.wrapper
  }

  save (blockContent) {
    console.log('SAVE')
    const inputUrl = blockContent.querySelector('[data-input-url]')
    const inputText = blockContent.querySelector('[data-input-text]')
    const selectColor = blockContent.querySelector('[data-select-color]')

    return Object.assign(this.data, {
      url: inputUrl.value,
      text: inputText.value,
      style: selectColor.value,
    })
  }
}

export default Button
