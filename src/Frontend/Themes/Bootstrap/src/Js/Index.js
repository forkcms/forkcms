// /* External library imports */
import * as bootstrap from 'bootstrap5/dist/js/bootstrap.js'
import '@fortawesome/fontawesome-free/js/all'
// import '@fancyapps/fancybox/dist/jquery.fancybox'
// /* Utilities imports */
import { ScrollTo } from './Utilities/ScrollTo'
import { Resize } from './Utilities/Resize'
// /* import {Fancybox} from './Utilities/Fancybox' */
// /* Theme imports */
// /* eg. import tooltip from './Theme/Tooltip' */
import { Pagination } from './Theme/Pagination'

/* Renders */
window.bootstrap = bootstrap
window.scrollto = new ScrollTo()
window.resizeFunction = new Resize()
window.pagination = new Pagination()

window.resizeFunction.resize()
window.pagination.events()
