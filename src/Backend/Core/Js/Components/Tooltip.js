export class Tooltip {
  constructor () {
    const $tooltip = $('[data-toggle="tooltip"]')

    if ($tooltip.length > 0) {
      $tooltip.tooltip()
    }
  }
}
