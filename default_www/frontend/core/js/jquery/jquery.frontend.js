/*!
 * jQuery Fork stuff
 */

/**
 * Share-button
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
			var link = document.location;
			var title = $('title').html();
			var description = '';
			var image = '';

			// get the link
			if($this.attr('href') != undefined) link = $this.attr('href');
			if(link.substr(0, 1) == '#') link = document.location;
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
							var url = 'http://delicious.com/save?url=' + link;
							if(title != '') url += '&title=' + title;
							if(description != '') url += '&notes=' + description;
							
							// add html
							html += '<li class="shareMenuDelicious"><a href="' + url + '" target="_blank"><span class="icon"><span class="textWrapper">' + options.delicious.label + '</span></a></li>' + "\n";
						break;
						
						// digg
						case 'digg':
							// build url
							var url = 'http://digg.com/submit?url=' + link;
							if(title != '') url += '&title=' + title;
							
							// add html
							html += '<li class="shareMenuDigg"><a href="' + url + '" target="_blank"><span class="icon"></span><span class="textWrapper">' + options.digg.label + '</span></a></li>' + "\n";
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
							var url = 'http://www.linkedin.com/shareArticle?mini=true&url=' + link;
							if(title != '') url += '&title=' + title;
							if(description != '') url += '&summary=' + description;

							// add html
							html += '<li class="shareMenuLinkedin"><a href="' + url + '" target="_blank"><span class="icon"></span><span class="textWrapper">' + options.linkedin.label + '</span></a></li>' + "\n";
						break;
						
						// netlog?
						case 'netlog':
							// build url
							var url = 'http://www.netlog.com/go/manage/links/view=save&origin=external&url=' + link;
							if(title != '') url += '&title=' + title;
							if(description != '') url += '&description=' + description;
							if(image != '') url += '&thumb=' + image;
							url += '&referer=' + document.location;
							
							// add html
							html += '<li class="shareMenuNetlog"><a href="' + url + '" target="_blank"><span class="icon"></span><span class="textWrapper">' + options.netlog.label + '</span></a></li>' + "\n";
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
							html += '<li class="shareMenuTwitter">';
							html += '<a href="http://twitter.com/share" class="twitter-share-button" data-url="' + link + '"';
							if(description != '') html += 'data-text="' + description + '"';
							html += 'data-count="none">' + options.twitter.label  + '</a>';
						break					
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
				$this.click(function(evt) 
				{ 
					// prevent default
					evt.preventDefault();
					
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