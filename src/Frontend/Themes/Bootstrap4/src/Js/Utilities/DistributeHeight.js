export class DistributeHeight {
  constructor ($element) {
    this.element = $element
    this.items = this.element.find('[data-mh]')
    this.breakpoint = this.element.data('distribute-height-breakpoint') ? this.element.data('distribute-height-breakpoint') : null
  }

  setHeights () {
    // Variables
    let $item
    let key
    let keys
    let height

    // Reset maxHeights object
    let maxHeights = []

    // Reset the height of all items so they can be recalculated
    this.items.height('auto')

    if ($(window).width() < this.breakpoint) {
      return
    }

    // Loop over each item
    this.items.each((index, item) => {
      $item = $(item)
      key = $item.data('mh')
      height = $item.height()

      // First, set the max-height for each type to zero (if it doesn't exist yet)
      if (!maxHeights.hasOwnProperty(key)) {
        maxHeights[key] = 0
      }

      // if the height of the current item is higher than the current maxHeight, overwrite the maxHeight for that type of item
      if (height >= maxHeights[key]) {
        maxHeights[key] = height
      }
    })

    // Set the height of the same types of items
    keys = Object.keys(maxHeights)
    for (let i = 0; i < keys.length; i++) {
      key = keys[i]
      this.element.find('[data-mh=' + key + ']').height(maxHeights[key])
    }
  }
}
