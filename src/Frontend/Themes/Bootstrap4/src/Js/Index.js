/* Bootstrap imports */
import 'bootstrap/dist/js/bootstrap'

/* Utilities imports */
import SweetScroll from 'sweet-scroll'
import { Resize } from './Utilities/Resize'
/* import {Fancybox} from './Utilities/Fancybox' */

/* Theme imports */
/* eg. import tooltip from './Theme/Tooltip' */
import { Pagination } from './Theme/Pagination'

/* Renders */
window.sweetscroll = new SweetScroll()
window.resizeFunction = new Resize()
window.pagination = new Pagination()

window.resizeFunction.resize()
window.pagination.events()
