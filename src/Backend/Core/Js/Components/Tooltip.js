export class Tooltip {
  constructor () {
    const $tooltip = $('[data-bs-toggle="tooltip"]')

    if ($tooltip.length > 0) {
      $tooltip.tooltip()
    }
  }
}
