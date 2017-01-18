export default class {

    constructor() {
        this.setHeights();
    }

    setHeights() {
        // Variables
        let $wrap,
            $this,
            $item,
            $items,
            key,
            keys,
            height,
            maxHeights;

        // The wrap
        $wrap = $('[data-distribute-height]');

        // Calculate the heights of the items grouped per wrap
        $wrap.each(function() {
            $this = $(this);
            $items = $this.find('[data-mh]');
            maxHeights = [];

            // Reset all heights, so we can recalculate them
            $items.height('auto');

            // Check each item in the wrap
            $items.each(function() {
                $item = $(this);
                key = $item.data('mh');
                height = $item.height();

                // First, set the max-height for each type to zero (if it doesn't exist yet)
                if (!maxHeights.hasOwnProperty(key)) maxHeights[key] = 0;

                // Check the height of the item, if it's higher than the previous one, overwrite the maxHeight for that type of item
                if (height >= maxHeights[key]) maxHeights[key] = height;
            });

            // Set the height of the same types of items
            keys = Object.keys(maxHeights);
            for (var i = 0; i < keys.length; i++) {
                key = keys[i];
                $this.find('[data-mh=' + key + ']').height(maxHeights[key]);
            }
        });
    }
}
