import { Data } from '../../../../../../../Core/assets/js/Components/Data'

export class Config {
  static isDebug () {
    return Data.get('debug')
  }

  static getCurrentLanguage () {
    return Data.get('LANGUAGE')
  }
}
