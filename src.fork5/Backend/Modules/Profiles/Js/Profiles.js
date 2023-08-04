import { MassAddToGroup } from './Components/MassAddToGroup'
import { EditEmail } from './Components/EditEmail'
import { Editpassword } from './Components/Editpassword'
import { Settings } from './Components/Settings'

export class Profiles {
  constructor () {
    this.massAddToGroup = new MassAddToGroup()
    this.editEmail = new EditEmail()
    this.editPassword = new Editpassword()
    this.settings = new Settings()
  }
}
