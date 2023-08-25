/**
 * Handles the privacy consent dialog
 */

import { Cookies } from '../../../../../../../Core/assets/js/Components/Cookies'
import { Data } from '../../../../../../../Core/assets/js/Components/Data'

export class ConsentDialog {
  constructor () {
    const $consentDialog = $('*[data-role=privacy_consent_dialog]')
    // if there is no consentDialog we shouldn't do anything
    if ($consentDialog.length === 0) return
    const $consentForm = $('form[data-role=privacy_consent_dialog_form]')

    $consentForm.on('click', '*[data-dismiss=modal]', (e) => {
      e.preventDefault()
      $consentDialog.hide()
    })

    $consentForm.on('submit', (e) => {
      e.preventDefault()

      const $levels = $consentForm.find('input[data-role=privacy-level]')
      for (const level of $levels) {
        const name = $(level).data('value')
        const isChecked = $(level).is(':checked')

        // store in jsData
        Data.get('privacyConsent.visitorChoices')[name] = isChecked

        // store for Google Tag Manager
        const niceName = name.charAt(0).toUpperCase() + name.slice(1)
        if (typeof window.dataLayer !== 'undefined') {
          if (isChecked) {
            const gtmData = {}
            gtmData['privacyConsentLevel' + niceName + 'Agreed'] = isChecked
            window.dataLayer.push(gtmData)
            window.dataLayer.push({ event: 'privacyConsentLevel' + niceName + 'Agreed' })
          }
        }

        // store data in functional cookies for later usage
        Cookies.setCookie('privacy_consent_level_' + name + '_agreed', isChecked ? 1 : 0, 6 * 30)
        Cookies.setCookie('privacy_consent_hash', Data.get('privacyConsent.levelsHash'), 6 * 30)

        // trigger events
        let eventName = 'privacyConsentLevel' + niceName
        if (isChecked) {
          eventName += 'Agreed'
        } else {
          eventName += 'Disagreed'
        }
        $(document).trigger(eventName)
      }

      $(document).trigger('privacyConsentChanged')

      $consentDialog.hide()
    })
  }
}
