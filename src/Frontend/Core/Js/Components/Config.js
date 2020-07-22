import { Data } from './Data'

export class Config {
  static getCurrentLanguage () {
    return Data.get('LANGUAGE')
  }
}
