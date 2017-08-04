/**
 * Interaction for the search module
 */
jsFrontend.search =
{

    // init, something like a constructor
    init: function()
    {
        // auto suggest (search widget)
        if($("input[data-role=fork-widget-search-field]").length > 0) jsFrontend.search.autosuggest(55);

        // autocomplete (search results page: autocomplete based on known search terms)
        if($("input[data-role=fork-search-field][data-autocomplete=enabled]").length > 0) jsFrontend.search.autocomplete();

        // live suggest (search results page: live feed of matches)
        if($("input[data-role=fork-search-field][data-live-suggest=enabled]").length > 0 && $('#searchContainer').length > 0) jsFrontend.search.livesuggest();
    },

    // autocomplete (search results page: autocomplete based on known search terms)
    autocomplete: function()
    {
        var input = $("input[data-role=fork-search-field][data-autocomplete=enabled]");
        var searchEngine = jsFrontend.search.getSuggestionEngine('Autocomplete');

        // autocomplete (based on saved search terms) on results page
        // Init the typeahead search
        input.typeahead(null, {
            name: 'search',
            display: 'value',
            hint: false,
            limit: 5,
            source: searchEngine,
            templates: {
                suggestion: function(data){
                    return '<a><strong>' + data.value + '</strong></a>';
                }
            }
        }).bind('typeahead:select', function(ev, suggestion) {
            window.location.href = suggestion.url;
        });

        // when we have been typing in the search textfield and we blur out of it, we're ready to save it
        input.on('blur', function()
        {
            if($(this).val() !== '')
            {
                // ajax call!
                $.ajax(
                {
                    data:
                    {
                        fork: { module: 'Search', action: 'Save' },
                        term: $(this).val()
                    }
                });
            }
        });
    },

    // auto suggest (search widget)
    autosuggest: function(length)
    {
        // set default values
        if(typeof length == 'undefined') length = 100;

        var input = $("input[data-role=fork-widget-search-field]");
        var searchEngine = jsFrontend.search.getSuggestionEngine('Autosuggest', length);

        // Init the typeahead search
        input.typeahead(null, {
            name: 'search',
            display: 'value',
            hint: false,
            limit: 5,
            source: searchEngine,
            templates: {
                suggestion: function(data){
                    return '<a><strong>' + data.value + '</strong><p>' + data.description + '</p></a>';
                }
            }
        }).bind('typeahead:select', function(ev, suggestion) {
            window.location.href = suggestion.url;
        });

        // when we have been typing in the search textfield and we blur out of it, we're ready to save it
        input.on('blur', function()
        {
            if($(this).val() !== '')
            {
                // ajax call!
                $.ajax(
                {
                    data:
                    {
                        fork: { module: 'Search', action: 'Save' },
                        term: $(this).val()
                    }
                });
            }
        });
    },

    // livesuggest (search results page: live feed of matches)
    livesuggest: function()
    {
        // check if calls for live suggest are allowed
        var allowCall = true;

        // grab element
        var $input = $("input[data-role=fork-search-field][data-live-suggest=enabled]");

        // change in input = do the dance: live search results completion
        $input.on('keyup', function()
        {
            var $searchContainer = $("*[data-role=search-results-container]");

            // make sure we're allowed to do the call (= previous call is no longer processing)
            if(allowCall)
            {
                // temporarily allow no more calls
                allowCall = false;

                // fade out
                $searchContainer.fadeTo(0, 0.5);

                // ajax call!
                $.ajax(
                {
                    data:
                    {
                        fork: { module: 'Search', action: 'Livesuggest' },
                        term: $(this).val()
                    },
                    success: function(data, textStatus)
                    {
                        // allow for new calls
                        allowCall = true;

                        // alert the user
                        if(data.code != 200 && jsFrontend.debug) { alert(data.message); }

                        if(data.code == 200)
                        {
                            // replace search results
                            $searchContainer.html(utils.string.html5(data.data));

                            // fade in
                            $searchContainer.fadeTo(0, 1);
                        }
                    },
                    error: function()
                    {
                        // allow for new calls
                        allowCall = true;

                        // replace search results
                        $searchContainer.html('');

                        // fade in
                        $searchContainer.fadeTo(0, 1);
                    }
                });
            }
        });
    },

    // Construct the search suggestion engine
    getSuggestionEngine: function(action, length)
    {
        var searchEngine = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: $.ajaxSettings.url,
                prepare: function (query, settings) {
                    settings.type = "POST";
                    settings.contentType = "application/x-www-form-urlencoded; charset=UTF-8";
                    settings.data = {
                        fork: {
                            module: 'Search',
                            action: action,
                            language: jsFrontend.current.language
                        },
                        term: query,
                        length: length
                    };
                    return settings;
                },
                filter: function (searchResults) {
                    // Map the remote source JSON array to a JavaScript array
                    return $.map(searchResults.data, function (result) {
                        if(action == 'Autocomplete') {
                            return {
                                value: result.term,
                                num_results: result.num_results,
                                url: result.url
                            };
                        } else {
                            return {
                                id: result.id,
                                value: result.title,
                                description: result.text,
                                url: result.full_url
                            };
                        }
                    });
                }
            }
        });
        return searchEngine;
    },
};

$(jsFrontend.search.init);
