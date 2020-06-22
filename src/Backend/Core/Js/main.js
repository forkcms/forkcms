// import styling
import '../Layout/Sass/screen.scss'

// import plugins
import 'bootstrap'
import 'bootstrap-tagsinput/dist/bootstrap-tagsinput.min'
import 'select2/dist/js/select2.full'

// component imports
import { Backend } from './components/backend'
//import { Utils } from './components/utils'
//import { Jbackend } from './components/jquery.backend'

$(window).on('load', () => {
  new Backend()
  //new Utils
  //new Jbackend
})
