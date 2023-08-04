import { Step2 } from './Step2'
import { Step4 } from './Step4'
import { Step5 } from './Step5'
import { Step6 } from './Step6'

export class Steps {
  constructor () {
    this.step2 = new Step2()
    this.step3 = new Step5()
    this.step4 = new Step4()
    this.step6 = new Step6()
  }
}
