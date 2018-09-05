/* Bootstrap imports */
import 'bootstrap/dist/js/bootstrap'

/* Utilities imports */
import { ScrollTo } from './Utilities/ScrollTo'
import { Resize } from './Utilities/Resize'
/* import {Fancybox} from './Utilities/Fancybox' */

/* Theme imports */
/* eg. import tooltip from './Theme/Tooltip' */
import { Pagination } from './Theme/Pagination'

/* Renders */
window.scrollto = new ScrollTo()
window.resizeFunction = new Resize()
window.pagination = new Pagination()

window.resizeFunction.resize()
window.pagination.events()
