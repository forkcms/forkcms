import Bloodhound from 'typeahead.js/dist/bloodhound'
import { StringUtil } from '../../../../Backend/Core/Js/Components/StringUtil'
import { Config } from '../../../Core/Js/Components/Config'

export class Search {
  constructor () {
    // auto suggest (search widget)
    if ($('input[data-role=fork-widget-search-field]').length > 0) this.autosuggest(55)

    // autocomplete (search results page: autocomplete based on known search terms)
    if ($('input[data-role=fork-search-field][data-autocomplete=enabled]').length > 0) this.autocomplete()

    // live suggest (search results page: live feed of matches)
    if ($('input[data-role=fork-search-field][data-live-suggest=enabled]').length > 0 && $('[data-role="search-results-container"]').length > 0) this.livesuggest()
  }

  // autocomplete (search results page: autocomplete based on known search terms)
  autocomplete () {
    const input = $('input[data-role=fork-search-field][data-autocomplete=enabled]')
    const searchEngine = this.getSuggestionEngine('Autocomplete')

    // autocomplete (based on saved search terms) on results page
    // Init the typeahead search
    input.typeahead(null, {
      name: 'search',
      display: 'value',
      hint: false,
      limit: 5,
      source: searchEngine,
      templates: {
        suggestion (data) {
          return '<a><strong>' + data.value + '</strong></a>'
        }
      }
    }).bind('typeahead:select', function (ev, suggestion) {
      window.location.href = suggestion.url
    })

    // when we have been typing in the search textfield and we blur out of it, we're ready to save it
    input.on('blur', (e) => {
      if ($(e.currentTarget).val() !== '') {
        // ajax call!
        $.ajax({
          data: {
            fork: {module: 'Search', action: 'Save'},
            term: $(e.currentTarget).val()
          }
        })
      }
    })
  }

  // auto suggest (search widget)
  autosuggest (length) {
    // set default values
    if (typeof length === 'undefined') length = 100

    const input = $('input[data-role=fork-widget-search-field]')
    const searchEngine = this.getSuggestionEngine('Autosuggest', length)

    // Init the typeahead search
    input.typeahead(null, {
      name: 'search',
      display: 'value',
      hint: false,
      limit: 5,
      source: searchEngine,
      templates: {
        suggestion (data) {
          return '<a><strong>' + data.value + '</strong><p>' + data.description + '</p></a>'
        }
      }
    }).bind('typeahead:select', (ev, suggestion) => {
      window.location.href = suggestion.url
    })

    // when we have been typing in the search textfield and we blur out of it, we're ready to save it
    input.on('blur', (e) => {
      if ($(e.currentTarget).val() !== '') {
        // ajax call!
        $.ajax({
          data: {
            fork: {module: 'Search', action: 'Save'},
            term: $(e.currentTarget).val()
          }
        })
      }
    })
  }

  // livesuggest (search results page: live feed of matches)
  livesuggest () {
    // check if calls for live suggest are allowed
    let allowCall = true

    // grab element
    const $input = $('input[data-role=fork-search-field][data-live-suggest=enabled]')

    // change in input = do the dance: live search results completion
    $input.on('keyup', (e) => {
      const $searchContainer = $('*[data-role=search-results-container]')

      // make sure we're allowed to do the call (= previous call is no longer processing)
      if (allowCall) {
        // temporarily allow no more calls
        allowCall = false

        // fade out
        $searchContainer.fadeTo(0, 0.5)

        // ajax call!
        $.ajax({
          data: {
            fork: {module: 'Search', action: 'Livesuggest'},
            term: $(e.currentTarget).val()
          },
          success (data, textStatus) {
            // allow for new calls
            allowCall = true

            // alert the user
            if (data.code !== 200 && Config.isDebug()) { window.alert(data.message) }

            if (data.code === 200) {
              // replace search results
              $searchContainer.html(StringUtil.html5(data.data))

              // fade in
              $searchContainer.fadeTo(0, 1)
            }
          },
          error () {
            // allow for new calls
            allowCall = true

            // replace search results
            $searchContainer.html('')

            // fade in
            $searchContainer.fadeTo(0, 1)
          }
        })
      }
    })
  }

  // Construct the search suggestion engine
  getSuggestionEngine (action, length) {
    const searchEngine = new Bloodhound({
      datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
      queryTokenizer: Bloodhound.tokenizers.whitespace,
      remote: {
        url: $.ajaxSettings.url,
        prepare (query, settings) {
          settings.type = 'POST'
          settings.contentType = 'application/x-www-form-urlencoded; charset=UTF-8'
          settings.data = {
            fork: {
              module: 'Search',
              action: action,
              language: Config.getCurrentLanguage()
            },
            term: query,
            length: length
          }
          return settings
        },
        filter (searchResults) {
          // Map the remote source JSON array to a JavaScript array
          return $.map(searchResults.data, (result) => {
            if (action === 'Autocomplete') {
              return {
                value: result.term,
                num_results: result.num_results,
                url: result.url
              }
            } else {
              return {
                id: result.id,
                value: result.title,
                description: result.text,
                url: result.full_url
              }
            }
          })
        }
      }
    })
    return searchEngine
  }
}
