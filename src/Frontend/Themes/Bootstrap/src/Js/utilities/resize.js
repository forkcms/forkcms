import requestAnimationFrame from 'requestanimationframe';
import distributeHeight from './distribute-height';

export default class {

    constructor() {
        this.resize();
    }

    resize() {
        let calculate,
            tick,
            ticking = false;

        calculate = function() {
            new distributeHeight();
            ticking = false;
        };

        tick = function() {
            if (!ticking) {
                requestAnimationFrame(calculate);
                ticking = true;
            }
        };
        tick();

        $(window).on('load resize', function() {
            tick();
        });
    }
}
