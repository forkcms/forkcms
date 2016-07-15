import requestAnimationFrame from 'requestanimationframe';

export class resizeFunction {

    constructor() {
        this.resize();
    }

    resize() {
        let calculate,
            tick,
            ticking = false;

        calculate = function() {
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
