$(document).ready(function() {
	$('.shareButtons').show();
	$('.shareButtons').css('visibility','hidden');
	function ShowShare() {
		$.ajaxSetup({ cache: true });
		$.getScript('https://apis.google.com/js/plusone.js');
		$.getScript('http://platform.twitter.com/widgets.js');
		$('.shareButtons').css('visibility','visible');
		$(this).unbind();
	};
	$(window).scroll(ShowShare);
	$('.share a')
		.removeAttr('href')
		.css('cursor','pointer')
		.hover(ShowShare)
	;
});


/**
 * jQWidon't v0.1 - Suppress typographic widows
 *   * http://davecardwell.co.uk/javascript/jquery/plugins/jquery-widont/0.1/
 *
 * Dave Cardwell <http://davecardwell.co.uk/>
 *
 * Built on the shoulders of giants:
 *   * John Resig <http://jquery.com/>
 *   * Shaun Inman <http://www.shauninman.com/plete/2006/08/...
 *                                             ...widont-wordpress-plugin.php>
 *
 *
 * Copyright (c) 2006 Dave Cardwell, dual licensed under the MIT and GPL
 * licenses:
 *   * http://www.opensource.org/licenses/mit-license.php
 *   * http://www.gnu.org/licenses/gpl.txt
 */


/**
 * For the latest version of this plugin, and a discussion of its usage and
 * implementation, visit:
 *   * http://davecardwell.co.uk/javascript/jquery/plugins/jquery-widont/
 */

new
function() {
    /**
     * The following functions and attributes form the Public interface of the
     * jQWidon't plugin, accessed externally through the $.jqwidont object.
     * See the relevant function definition later in the source for further
     * information.
     *
     * $.jqwidont.auto()
     * $.jqwidont.auto( bool )
     * $.jqwidont.init()
     *
     * $.jqwidont.transform( string )
     */
    var Public = {
        // Given a boolean argument, auto() will turn initialisation at
        // $(document).ready() on or off accordingly.  Without, it simply
        // returns the current status.  init() performs the necessary steps to
        // start the jQWidon't plugin, including suppressing widows in all
        // <h*> elements in the page.
        'auto': function(bool) {
            return bool != undefined ? Private.auto = bool : Private.auto;
        },
        'init': function() {
            return Private.init();
        },

        // Perform the 'widont' transformation on a given string.
        'transform': function(string) {
            return Private.widont(string);
        }
    };

    // Allow external access to the 'Public' interface through the $.jqwidont
    // object.
    $.jqwidont = Public;



    /**
     * The following functions and attributes form the internal methods and
     * state of the jQWidon't plugin.  See the relevant function definition
     * later in the source for further information.
     *
     * Private.auto
     * Private.init()
     *
     * Private.widont( string )
     *
     * Private.regexp
     */
    var Private = {
        // When auto is true (default), the init() function will be called at
        // $(document).ready().  init() itself performs the necessary steps to
        // start the jQWidon't plugin.
        'auto': true,
        'init': init,

        // Add a non-breaking space between the last two words of a given
        // string.
        'widont': widont,

        // Regular expression for use later in the plugin.
        'regexp': new RegExp('[\\n\\r\\s]+' // whitespace/newlines
        + '(' // capture...
        + '[^\\n\\r\\s(?:&#160;)]+' // non-whitespace/newlines
        + '[\\n\\r\\s]*' // trailing whitespace
        + ')$' // ...to end of the string
        , 'm' // match across newlines
        )
    };



    /**
     * Unless disabled before $(document).ready() is triggered, execute the
     * init() function.
     */
    $(document).ready(function() {
        if (Private.auto) init();
    });


    /**
     * Perform the necessary initialisation to use the jQWidon't plugin.
     */

    function init() {
        // Use the plugin on all <h*> elements in the page..
        $('h1,h2,h3,h4,h5,h6').widont();
    };



    /**
     * Use the jQWidon't plugin on the given elements.
     */
    $.fn.widont = function() {
        return $(this).each(function() {
            var $obj = $(this);

            $obj.html(Private.widont($obj.html()));
        });
    };



    /**
     * This function takes a string and uses the Private.regexp function to
     * attempt to replace the whitespace between the final two words with a
     * non-breaking space, so that line-wrapping cannot occur between them.
     */

    function widont(string) {
        return string.replace(Private.regexp, "&#160;$1");
    };
}();