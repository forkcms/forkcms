import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js'

export class TwoFactorAuthorization {
  constructor (ajax) {
    this._ajax = ajax
    this.init()
  }

  init () {
    document.querySelectorAll('[data-role="enable-2fa"]').forEach((element) => {
      element.addEventListener('click', (e) => {
        e.preventDefault()

        this.enable2fa()
      })
    })

    document.querySelectorAll('[data-role="next"]').forEach((element) => {
      element.addEventListener('click', (e) => {
        e.preventDefault()

        this.showForm()
      })
    })

    document.querySelectorAll('[data-role="two-factor-authorization-code"]').forEach((element) => {
      element.addEventListener('input', (e) => {
        e.preventDefault()

        document.querySelector('[data-role="enable-two-factor-authorization-button"]').disabled = false
      })
    })

    document.querySelector('[data-role="enable-two-factor-authorization-button"]').addEventListener('click', (e) => {
      e.preventDefault()

      this.confirm2fa()
    })
  }

  showForm () {
    const form = document.querySelector('#two-factor-authorization-modal form')
    form.classList.remove('d-none')
    document.querySelector('[data-role="enable-two-factor-authorization-button"]').classList.remove('d-none')
    document.querySelector('[data-role="two-factor-authorization-code"]').focus()
    document.querySelector('[data-role="next"]').classList.add('d-none')
  }

  confirm2fa () {
    const form = document.querySelector('#two-factor-authorization-modal form')
    // eslint-disable-next-line no-undef
    const formEntries = new FormData(form).entries()
    const json = Object.assign(...Array.from(formEntries, ([x,y]) => ({[x]:y})))

    this._ajax.makeRequest(
      {
        module: 'backend',
        action: 'ajax_action_confirm_two_factor_authorization_code',
        parameters: JSON.stringify(json)
      },
      function (data) {
        if (!data.backupCodes) {
          document.querySelector('[data-role="confirm-2fa-error"]').classList.remove('d-none')

          return
        }

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('two-factor-authorization-modal'))
        modal.hide()

        const codes = []
        data.backupCodes.forEach((code) => {
          codes.push(`<code>${code}</code>`)
        })

        // Put the backupcodes in the [data-role="backup-codes"] element
        document.querySelector('[data-role="backup-codes"]').innerHTML = codes.join('\n')

        // Show the backup codes modal
        const backupCodesModal = new bootstrap.Modal('#two-factor-authorization-backup-codes-modal')
        backupCodesModal.show()
      }
    )
  }

  enable2fa () {
    this._ajax.makeRequest(
      {
        module: 'backend',
        action: 'ajax_action_get_two_factor_authorization_code'
      },
      function (data) {
        const modal = new bootstrap.Modal('#two-factor-authorization-modal')
        const image = document.querySelector('#qr-code')
        image.src = data.qrCode

        document.querySelector('[data-role="two-factor-authorization-secret"]').value = data.secret

        modal.show()
      }
    )
  }
}
