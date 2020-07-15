export class PasswordGenerator {
  static generator (options, element) {
    // define defaults
    const defaults = {
      length: 6,
      uppercase: true,
      lowercase: true,
      numbers: true,
      specialchars: false,
      generateLabel: 'Generate'
    }

    // extend options
    options = $.extend(defaults, options)

    return $(element).each((index, el) => {
      const id = $(el).attr('id')

      // append the button
      $(el).closest('.input-group input').after(
        '        <span class="input-group-append"><button data-id="' + id + '" class="generatePasswordButton btn btn-primary" type="button"><span>' + options.generateLabel + '</span></button></span>'
      )

      $('.generatePasswordButton').on('click', generatePassword)

      function generatePassword (e) {
        // prevent default
        e.preventDefault()

        const currentElement = $('#' + $(this).data('id'))

        // check if it isn't a text-element
        let newElement
        if (currentElement.attr('type') !== 'text') {
          // clone the current element
          newElement = $('<input value="" id="' + currentElement.attr('id') + '" name="' + currentElement.attr('name') + '" maxlength="' + currentElement.attr('maxlength') + '" class="' + currentElement.attr('class') + '" type="text">')

          // insert the new element
          newElement.insertBefore(currentElement)

          // remove the current one
          currentElement.remove()
        } else {
          // already a text element
          newElement = currentElement
        }

        // generate the password
        const pass = generatePass(options.length, options.uppercase, options.lowercase, options.numbers, options.specialchars)

        // set the generate password, and trigger the keyup event
        newElement.val(pass).keyup()
      }

      function generatePass (length, uppercase, lowercase, numbers, specialchars) {
        // the vowels
        const v = ['a', 'e', 'u', 'ae', 'ea']

        // the consonants
        const c = ['b', 'c', 'd', 'g', 'h', 'j', 'k', 'm', 'n', 'p', 'r', 's', 't', 'u', 'v', 'w', 'tr', 'cr', 'fr', 'dr', 'wr', 'pr', 'th', 'ch', 'ph', 'st']

        // the number-mapping
        const n = []
        n['a'] = 4
        n['b'] = 8
        n['e'] = 3
        n['g'] = 6
        n['l'] = 1
        n['o'] = 0
        n['s'] = 5
        n['t'] = 7
        n['z'] = 2

        // the special chars-mapping
        const s = []
        s['a'] = '@'
        s['i'] = '!'
        s['c'] = 'ç'
        s['s'] = '$'
        s['g'] = '&'
        s['h'] = '#'
        s['l'] = '|'
        s['x'] = '%'

        // init vars
        let pass = ''
        let tmp = ''

        // add a random consonant and vowel as longs as the length isn't reached
        for (let i = 0; i < length; i++) {
          tmp += c[Math.floor(Math.random() * c.length)] + v[Math.floor(Math.random() * v.length)]
        }

        // convert some chars to uppercase
        for (let i = 0; i < length; i++) {
          if (Math.floor(Math.random() * 2)) {
            pass += tmp.substr(i, 1).toUpperCase()
          } else {
            pass += tmp.substr(i, 1)
          }
        }

        // numbers allowed?
        if (numbers) {
          tmp = ''
          for (let i in pass) {
            // replace with a number if the random number can be devided by 3
            if (typeof n[pass[i].toLowerCase()] !== 'undefined' && (Math.floor(Math.random() * 4) % 3) === 1) {
              tmp += n[pass[i].toLowerCase()]
            } else {
              tmp += pass[i]
            }
          }
          pass = tmp
        }

        // special chars allowed
        if (specialchars) {
          tmp = ''
          for (let i in pass) {
            // replace with a special number if the random number can be devided by 2
            if (typeof s[pass[i].toLowerCase()] !== 'undefined' && (Math.floor(Math.random() * 4) % 2)) {
              tmp += s[pass[i].toLowerCase()]
            } else {
              tmp += pass[i]
            }
          }
          pass = tmp
        }

        // if uppercase isn't allowed we convert all to lowercase
        if (!uppercase) pass = pass.toLowerCase()

        // if lowercase isn't allowed we convert all to uppercase
        if (!lowercase) pass = pass.toUpperCase()

        // return
        return pass
      }
    })
  }
}
