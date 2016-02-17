;(function ( $, window, document, undefined ) {

	"use strict";

		var pluginName = "shareBtns";

		/**
		 * Add Share Buttons on any DOM element.
		 * Simple set the data-share attribute to the social network you
		 * want to share to.
		 * @example
		 * <a href="#" data-share="facebook">Share on Facebook</a>
     * Use data-url to share an alternative url, otherwise the current page is shared
     * <a href="#" data-share="facebook" url="http://www.sumocoders.be">Share on Facebook</a>
     * Twitter has extra options:
     * <a href="#" data-share="twitter" via="sumocoders" hashtags="sumocoders" text="Check this out!">Share on Twitter</a>
		 */

		function ShareBtns ( element ) {
			this.element = element;
			this.init();
		}

		$.extend(ShareBtns.prototype, {
			init: function () {
        $('[data-share]').on('click', function() {
          this.clickShare(event);
        }.bind(this));
			},
      clickShare: function(e) {
        e.preventDefault();
				var $element = $(event.target);

				try {
					window.open(this.getShareUrl($element), '', this.getPopupParams());
				}
				catch (error) {
					console.error(error);
				}
      },
			getShareUrl: function($element) {
				var sharer = this.getSharer($element);
				return sharer.shareUrl + '?' + this.getSharerParams(sharer);
			},
			getSharer: function($element) {
				var url = $element.data('url') || window.location.href;
				var share = $element.data('share');
				var sharers = {
					facebook: {
						shareUrl: 'https://www.facebook.com/sharer/sharer.php',
						params: {
							u: url
						}
					},
					twitter: {
						shareUrl: 'https://twitter.com/intent/tweet',
						params: {
							url: url,
							text: $element.data('text'),
							hashtags: $element.data('hashtags'),
							via: $element.data('via')
						}
					},
					linkedin: {
						shareUrl: 'https://www.linkedin.com/shareArticle',
						params: {
							url: url,
							mini: true
						}
					},
					googleplus: {
						shareUrl: 'https://plus.google.com/share',
						params: {
							url: url
						}
					}
				};

				if (sharers[share]) {
					return sharers[share];
				} else {
					throw "Sharer not found";
				}
			},
      getSharerParams: function(sharer) {
        return Object.keys(sharer.params)
				.filter(function(key) {
					if(typeof sharer.params[key] === 'undefined') {
						return false;
					}
					return true;
				})
				.map(function(key) {
	        return key + '=' + encodeURIComponent(sharer.params[key]);
        }).reduce(function(previous, current) {
          return previous + '&' + current;
        });
      },
      getPopupParams: function() {
        var width  = 575,
            height = 400,
            left   = ($(window).width()  - width)  / 2,
            top    = ($(window).height() - height) / 2;

        return 'status=1' +
               ',width='  + width  +
               ',height=' + height +
               ',top='    + top    +
               ',left='   + left;
      }
		});

		$.fn[ pluginName ] = function ( options ) {
			return this.each(function() {
				if ( !$.data( this, "plugin_" + pluginName ) ) {
					$.data( this, "plugin_" + pluginName, new ShareBtns( this, options ) );
				}
			});
		};

})( jQuery, window, document );

$(document).shareBtns();
