/*!
 * jQuery Fork stuff
 */

/**
 * Share-button
 * This plugin will convert a single item into a list with share-options.
 *
 * What information will be used.
 * - Url that will be shared: if there is a href-attribute this value will be used as url, if not the url of the current page will be used
 * - The title of the item:
 * 		1. if the OpenGraph-title-tag is provided, the value of the tag is used.
 * 		2. if the title-attribute is used this value will be used in favor of the OpenGraph-title-tag.
 * 		3. if the data-title-attribute is set this value will be used, OpenGraph and title-attribute will be ignored.
 * 		4. as a fallback the value of the title-tag is used.
 * - The description (used by: delicious, linkedin, netlog, twitter)
 * 		1. if the OpenGraph-description-tag is provided, that value will be used.
 * 		2. if the data-description-attribute is set this value will be used, even if the OpenGraph-image-tag is provided.
 * 		3. no description will be used.
 * - The image (used by: netlog)
 * 		1. if the OpenGraph-image tag is available the value of this tag will be used.
 * 		2. if the data-image-attribute is set this value will be used, even if the OpenGraph-image-tag is provided.
 * 		3. the default image if there is one provided.
 * 		4. no image will be used .
 *
 * Possible options:
 * - debug			if debug is enabled a warning will be logged to the console if og-parameters aren't available, possible values are: true, false.
 * - default_image	the image that will be used by default.
 * - sequence		an array containing the names of the share-items in the wanted sequence.
 * - isDropdown		will the plugin be used as a dropdownmenu? If so we will hide it by default an show on click/hover.
 *
 * @author	Tijs Verkoyen <tijs@sumocoders.be>
 */
(function($)
{
	$.fn.shareMenu = function(options)
	{
		var twitterLoaded = false;

		// define defaults
		var defaults =
		{
			debug: false,
			default_image: document.location.protocol + '//' + document.location.host + '/apple-touch-icon.png',
			sequence: ['facebook', 'twitter', 'netlog', 'linkedin', 'digg', 'delicious'],
			isDropdown: true,

			delicious: { name: 'delicious', show: true, label: 'Delicious'},
			digg: { name: 'digg', show: true, label: 'Digg' },
			facebook: { name: 'facebook', show: true, width: 90, verb: 'like', colorScheme: 'light', font : 'arial' },
			linkedin: { name: 'linkedin', show: true, label: 'LinkedIn' },
			netlog: { name: 'netlog', show: true, label: 'Netlog' },
			twitter: { name: 'twitter', show: true, label: 'tweet' }
		};

		// extend options
		var options = $.extend(defaults, options);

		return this.each(function()
		{
			// grab current element
			var $this = $(this);

			// init vars
			var link = document.location.href;
			var title = $('title').html();
			var description = '';
			var image = '';

			// get the link
			if($this.attr('href') != undefined) link = $this.attr('href');
			if(link.substr(0, 1) == '#') link = document.location.href;
			if(link.substr(0, 4) != 'http') link = document.location.protocol + '//' + document.location.host + link;

			// get the title
			if($('meta[property="og:title"]').attr('content') != undefined) title = $('meta[property="og:title"]').attr('content');
			if($this.attr('title') != undefined) title = $this.attr('title');
			if($this.data('title') != undefined) title = $this.data('title');

			// get the description
			if($('meta[property="og:description"]').attr('content') != undefined) description = $('meta[property="og:description"]').attr('content');
			if($this.data('description') != undefined) description = $this.data('description');

			// get the image
			if($('meta[property="og:image"]').attr('content') != undefined) image = $('meta[property="og:image"]').attr('content');
			if($this.data('image') != undefined) image = $this.data('image');
			if(image == '' && options.default_image != '') image = options.default_image;

			// start HTML
			if(options.isDropdown) var html = '<ul style="display: none;" class="shareMenu">' + "\n";
			else var html = '<ul class="shareMenu">' + "\n";

			// loop items
			for(var i in options.sequence)
			{
				// is the option enabled?
				if(options[options.sequence[i]].show)
				{
					// based on the type we should generate the correct markup
					switch(options[options.sequence[i]].name)
					{
						// delicious
						case 'delicious':
							// build url
							var url = 'http://delicious.com/save?url=' + encodeURIComponent(link);
							if(title != '') url += '&title=' + title;
							if(description != '') url += '&notes=' + description;

							// add html
							html += '<li class="shareMenuDelicious">' +
									'	<a href="' + url + '" target="_blank">' +
									'		<span class="icon"></span>' +
									'		<span class="textWrapper">' + options.delicious.label + '</span>' +
									'	</a>' +
									'</li>' + "\n";
						break;

						// digg
						case 'digg':
							// build url
							var url = 'http://digg.com/submit?url=' + encodeURIComponent(link);
							if(title != '') url += '&title=' + title;

							// add html
							html += '<li class="shareMenuDigg">' +
									'	<a href="' + url + '" target="_blank">' +
									'		<span class="icon"></span>' +
									'		<span class="textWrapper">' + options.digg.label + '</span>' +
									'	</a>' +
									'</li>' + "\n";
						break;

						// facebook?
						case 'facebook':
							// check for OG-data.
							if(options.debug && $('meta[property^="og"]').length == 0) console.log('You should provide OpenGraph data.');

							// add html
							html += '<li class="shareMenuFacebook">';

							// check if the FB-object is available
							if(typeof FB != 'object')
							{
								html += '<iframe src="http://www.facebook.com/plugins/like.php?href=' + link + '&amp;send=false&amp;layout=button_count&amp;width=' + options.facebook.width + '&amp;show_faces=false&amp;action=' + options.facebook.verb + '&amp;colorscheme=' + options.facebook.colorScheme + '&amp;font=' + options.facebook.font + '&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:' + options.facebook.width + 'px; height:21px;" allowTransparency="true"></iframe>';
							}
							else
							{
								html += '<fb:like href="' + link + '" send="false" layout="button_count" width="' + options.facebook.width + '" show_face="false" action="' + options.facebook.verb + '" colorscheme="' + options.facebook.colorScheme + '" font="' + options.facebook.font  + '"></fb:like>' + "\n";
							}

							// end html
							html += '</li>';
						break;

						// linkedin
						case 'linkedin':
							// build url
							var url = 'http://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(link);
							if(title != '') url += '&title=' + title;
							if(description != '') url += '&summary=' + description;

							// add html
							html += '<li class="shareMenuLinkedin">' +
									'	<a href="' + url + '" target="_blank">' +
									'		<span class="icon"></span>' +
									'		<span class="textWrapper">' + options.linkedin.label + '</span>' +
									'	</a>' +
									'</li>' + "\n";
						break;

						// netlog?
						case 'netlog':
							// build url
							var url = 'http://www.netlog.com/go/manage/links/view=save&origin=external&url=' + encodeURIComponent(link);
							if(title != '') url += '&title=' + title;
							if(description != '') url += '&description=' + description;
							if(image != '') url += '&thumb=' + encodeURIComponent(image);
							url += '&referer=' + encodeURIComponent(document.location);

							// add html
							html += '<li class="shareMenuNetlog">' +
									'	<a href="' + url + '" target="_blank">' +
									'		<span class="icon"></span>' +
									'		<span class="textWrapper">' + options.netlog.label + '</span>' +
									'	</a>' +
									'</li>' + "\n";
						break;

						// twitter
						case 'twitter':
							if(!twitterLoaded) {
								// loop all script to check if the twitter-widget is already loaded
								$('script').each(function()
								{
									if($(this).attr('src') == 'http://platform.twitter.com/widgets.js') twitterLoaded = true;
								});

								// not loaded?
								if(!twitterLoaded)
								{
									// create the script tag
									var script = document.createElement('script')
									script.src = 'http://platform.twitter.com/widgets.js';

									// add into head
									$('head').after(script);

									// reset var
									twitterLoaded = true;
								}
							}

							// build & add html
							html += '<li class="shareMenuTwitter">' +
									'	<a href="http://twitter.com/share" class="twitter-share-button" data-url="' + link + '"';
							if(title != '') html += ' data-text="' + title + '"';
							html += ' data-lang="' + jsFrontend.current.language + '"';
							html += ' data-count="none">' + options.twitter.label  + '</a>';
							html += '</li>';
						break;
					}
				}
			}

			// end html
			html += '</ul>';

			// append to current element
			$this.append(html);

			if(options.isDropdown)
			{
				// bind click
				$this.on('click', function(e)
				{
					// toggle menu
					$this.find('ul.shareMenu').toggle();
				});

				$this.hover(
					function()
					{
						$this.find('ul.shareMenu').show();
					},
					function()
					{
						$this.find('ul.shareMenu').hide();
					}
				);
			}
		});
	};
})(jQuery);