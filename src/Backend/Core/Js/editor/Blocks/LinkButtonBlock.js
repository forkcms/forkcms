class LinkButtonBlock {
  constructor({data, config, api}) {
    this.api = api;
    const capitalize = str => str[0].toUpperCase() + str.substr(1);

    this.data = {
      text: data.text || capitalize(jsBackend.locale.lbl('ChangeThisText')),
      url: data.url || '#',
      type: Object.values(LinkButtonBlock.TYPES).includes(data.type) && data.type || LinkButtonBlock.DEFAULT_TYPE
    };
  }

  static get toolbox() {
    return {
      title: 'LinkButton',
      icon: '<i class="fas fa-external-link-alt"></i>'
    }
  }

  static get TYPES() {
    return {
      default: 'default',
      success: 'success',
      info: 'info',
      warning: 'warning',
      danger: 'danger',
    };
  }

  static get DEFAULT_TYPE() {
    return LinkButtonBlock.TYPES.default;
  }

  get settings() {
    return Object.keys(LinkButtonBlock.TYPES).map(function(type) {
      let textClass = type
      if (textClass === LinkButtonBlock.DEFAULT_TYPE) {
        textClass = 'muted'
      }

      return {
        name: type,
        icon: `<i class="fas fa-circle text-${textClass}"></i>`
      }
    })
  }

  renderSettings() {
    const wrapper = this._make('div', [this.api.styles.settingsWrapper], {});
    const capitalize = str => str[0].toUpperCase() + str.substr(1);

    this.settings
      .map(type => {
        const el = this._make('div', this.api.styles.settingsButton, {
          innerHTML: type.icon,
          title: `${capitalize(type.name)} type`
        });

        el.classList.toggle('border', type.name === this.data.type);
        el.classList.toggle('border-primary', type.name === this.data.type);

        wrapper.appendChild(el);

        return el;
      })
      .forEach((element, index, elements) => {
        element.addEventListener('click', () => {
          this._changeStyle(this.settings[index].name);

          elements.forEach((el, i) => {
            const {name} = this.settings[i];

            el.classList.toggle('border', name === this.data.type);
            el.classList.toggle('border-primary', name === this.data.type);
          });
        });
      });

    return wrapper;
  };

  render() {
    const capitalize = str => str[0].toUpperCase() + str.substr(1);

    const container = this._make('div', [this.api.styles.block, 'cdx-link-button', 'p-1']);
    const urlInputGroup = this._make('label', ['input-group', 'flex-nowrap']);
    const urlInputGroupPrepend = this._make('div', ['input-group-prepend']);
    const urlInputGroupPrependText = this._make('span', ['input-group-text']);
    urlInputGroupPrependText.innerHTML = capitalize(jsBackend.locale.lbl('URL'))

    this.preview = this._make('a', ['btn', 'btn-' + this.data.type, 'mb-1'], {
      href: this.data.url,
      contentEditable: true,
    })
    this.preview.innerHTML = this.data.text
    this.textInput = this._make('input', [this.api.styles.input, 'form-control'], {
      autocomplete: 'off',
      value: this.data.text
    });

    this.urlInput = this._make('input', [this.api.styles.input, 'form-control'], {
      autocomplete: 'off',
      value: this.data.url
    });

    container.appendChild(this.preview)
    container.appendChild(urlInputGroup);
    urlInputGroup.appendChild(urlInputGroupPrepend);
    urlInputGroupPrepend.appendChild(urlInputGroupPrependText);
    urlInputGroup.appendChild(this.urlInput);

    return container;
  }

  save(blockContent) {
    this.preview.setAttribute('href', this.urlInput.value)

    return Object.assign(this.data, {
      text: this.preview.innerHTML,
      url: this.urlInput.value
    })
  }

  /**
   * Change the buttons style
   *
   * @param {string} style
   * @private
   */
  _changeStyle(style) {
    this.preview.classList.toggle('btn-' + this.data.type)
    this.data.type = style;
    this.preview.classList.toggle('btn-' + this.data.type)
  }

  /**
   * Helper for making Elements with attributes
   *
   * @param  {string} tagName           - new Element tag name
   * @param  {array|string} classNames  - list or name of CSS classname(s)
   * @param  {Object} attributes        - any attributes
   * @return {Element}
   */
  _make(tagName, classNames = null, attributes = {}) {
    let el = document.createElement(tagName);

    if (Array.isArray(classNames)) {
      el.classList.add(...classNames);
    }
    else if (classNames) {
      el.classList.add(classNames);
    }

    for (let attrName in attributes) {
      el[attrName] = attributes[attrName];
    }

    return el;
  }
}

export default LinkButtonBlock
