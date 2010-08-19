/**
 * Meta-handler
 * 
 * @author Tijs Verkoyen <tijs@netlash.com>
 */
(function($) {
	$.fn.doMeta = function(options) {
		// define defaults
		var defaults = {};
		
		// extend options
		var options = $.extend(defaults, options);
	
		// loop all elements
		return this.each(function() {
			// init var
			var element = $(this);
			
			// initialize
			calculateMeta(null, $(this));
			
			// bind keypress
			$(this).bind('keyup', calculateMeta);
			
			// bind change on the checkboxes
			if($('#pageTitle').length > 0 && $('#pageTitleOverwrite').length > 0) {
				$('#pageTitleOverwrite').change(function(evt) { 
					if(!$(this).is(':checked')) {
						$('#pageTitle').val(element.val());
					}
				});
			}
			if($('#navigationTitle').length > 0 && $('#navigationTitleOverwrite').length > 0) {
				$('#navigationTitleOverwrite').change(function(evt) { 
					if(!$(this).is(':checked')) {
						$('#navigationTitle').val(element.val());
					}
				});
			}
			$('#metaDescriptionOverwrite').change(function(evt) { 
				if(!$(this).is(':checked')) {
					$('#metaDescription').val(element.val());
				}
			});
			$('#metaKeywordsOverwrite').change(function(evt) { 
				if(!$(this).is(':checked')) {
					$('#metaKeywords').val(element.val());
				}
			});
			$('#urlOverwrite').change(function(evt) { 
				if(!$(this).is(':checked')) {
					$('#url').val(utils.string.urlise(element.val())); 
					$('#generatedUrl').html(utils.string.urlise(element.val())); 
				}
			});

			// calculate meta
			function calculateMeta(evt, element) {
				if(typeof element != 'undefined') var title = element.val(); 
				else var title = $(this).val();

				if($('#pageTitle').length > 0 && $('#pageTitleOverwrite').length > 0) {
					if(!$('#pageTitleOverwrite').is(':checked')) {
						$('#pageTitle').val(title);
					}
				}
				if($('#navigationTitle').length > 0 && $('#navigationTitleOverwrite').length > 0) {
					if(!$('#navigationTitleOverwrite').is(':checked')) {
						$('#navigationTitle').val(title);
					}
				}
				if(!$('#metaDescriptionOverwrite').is(':checked')) {
					$('#metaDescription').val(title);
				}
				if(!$('#metaKeywordsOverwrite').is(':checked')) {
					$('#metaKeywords').val(title);
				}
				if(!$('#urlOverwrite').is(':checked')) {
					$('#url').val(utils.string.urlise(title));
					$('#generatedUrl').html(utils.string.urlise(title));
				}
			}
		});
	};
})(jQuery);


/**
 * Inline editing
 * 
 * @author Dave Lens <dave@netlash.com>
 * @author Tijs Verkoyen <tijs@netlash.com>
 */
(function($) {
	$.fn.inlineTextEdit = function(options) {
		// define defaults
		var defaults = {
			saveUrl: null,
			current: {},
			extraParams: {},
			inputClasses: 'inputText',
			allowEmpty: false,
			tooltip: 'click to edit'
		};
		
		// extend options
		var options = $.extend(defaults, options);
	
		// loop all elements
		return this.each(function() {
			// add wrapper and tooltip
			$(this).html('<span>'+ $(this).html() +'</span><span style="display: none;" class="inlineEditTooltip">'+ options.tooltip +'</span>');
			
			// grab element
			var element = $($(this).find('span')[0]);
			
			// bind events
			element.bind('click focus', createElement);
			$(this).hover(function() { 
							$(this).addClass('inlineEditHover');
							$($(this).find('span')[1]).show();
						}, 
						function() { 
							$(this).removeClass('inlineEditHover'); 
							$($(this).find('span')[1]).hide();
						});
			
			// create an element
			function createElement() {
				// grab current value
				options.current.value = element.html();
				
				// grab extra params
				if($(this).parent().attr('rel') != '') options.current.extraParams = eval('('+ $(this).parent().attr('rel') +')');
				
				// add class
				element.addClass('inlineEditing');
				
				// remove events
				element.unbind('click').unbind('focus');
				
				// set html
				element.html('<input type="text" class="'+ options.inputClasses +'" value="'+ options.current.value +'" />');
				
				// store element
				options.current.element = $(element.find('input')[0]);
				
				// set focus
				options.current.element.select();
				
				// bind events
				options.current.element.bind('blur', saveElement);
				options.current.element.keyup(function(evt) {
					// handle escape
					if(evt.which == 27) {
						// reset
						options.current.element.val(options.current.value);
						
						// destroy
						destroyElement();
					}
					
					// save when someone presses enter
					if(evt.which == 13) saveElement();
				});
			}
			
			// destroy the element
			function destroyElement() {
				// get parent
				var parent = options.current.element.parent();
				
				// set HTML and rebind events
				parent.html(options.current.element.val())
						.bind('click focus', createElement);
				
				// add class
				parent.removeClass('inlineEditing');
			}
			
			// save the element
			function saveElement() {
				// if the new value is empty and that isn't allowed, we restore
				// the original value
				if(!options.allowEmpty && options.current.element.val() == '') {
					options.current.element.val(options.current.value);
				}
				
				// is the value different from the original value
				if(options.current.element.val() != options.current.value)
				{
					// add element to the params
					options.current.extraParams['value'] = options.current.element.val(); 

					// make the call
					$.ajax({ url: options.saveUrl, data: options.current.extraParams,
						success: function(data, textStatus) { 
							// destroy the element
							destroyElement();
						}
					});
				}
				
				// destroy the element
				else destroyElement();
			}
		});
	};
})(jQuery);


/**
 * Tag-box
 * 
 * @author Tijs Verkoyen <tijs@netlash.com>
 */
(function($) {
	$.fn.tagBox = function(options) {
		// define defaults
		var defaults = {
			splitChar: ',',
			emptyMessage: '',
			addLabel: 'add',
			removeLabel: 'delete',
			autoCompleteUrl: '',
			canAddNew: false,
			showIconOnly: true
		};
		
		// extend options
		var options = $.extend(defaults, options);
	
		// loop all elements
		return this.each(function() {
			// define some vars
			var id = $(this).attr('id');
			var elements = get();
			var blockSubmit = false;

			// bind submit
			$(this.form).submit(function() { return !blockSubmit; });
			
			// build replace html
			var html = 	'<div class="tagsWrapper">'+
						'	<div class="oneLiner">'+
						'		<p><input class="inputText dontSubmit" id="addValue-'+ id +'" name="addValue-'+ id +'" type="text" /></p>'+
						'		<div class="buttonHolder">'+
						'			<a href="#" id="addButton-'+ id +'" class="button icon iconAdd disabledButton';
			if(options.showIconOnly) html += ' iconOnly';
			html +=		'">'+
						'				<span>'+ options.addLabel +'</span>'+
						'			</a>'+
						'		</div>'+
						'	</div>'+
						'	<div id="elementList-'+ id +'" class="tagList">'+
						'	</div>'+
						'</div>';
			
			// hide current element
			$(this).css('visibility', 'hidden')
					.css('position', 'absolute')
					.css('top', '-9000px')
					.css('left', '-9000px')
					.attr('tabindex', '-1');
			
			// prepend html
			$(this).before(html);

			// add elements list
			build();
			
			// bind autocomplete if needed
			if(options.autoCompleteUrl != '') {
				$('#addValue-'+ id).autocomplete({
						delay: 200,
						minLength: 2,
						source: function(request, response) {
							$.ajax({ url: options.autoCompleteUrl, type: 'GET',
								data: 'term=' + request.term,
								success: function(data, textStatus) {
									// init var
									var realData = [];

									// alert the user
									if(data.code != 200 && jsBackend.debug) { alert(data.message); }
									if(data.code == 200) {
										for(var i in data.data) realData.push({ label: data.data[i].name, value: data.data[i].name });
									}
									
									// set response
									response(realData);
								}
							});
						}
					});
			}
			
			// bind keypress on value-field
			$('#addValue-'+ id).bind('keyup', function(evt) {
				blockSubmit = true;
				
				// grab code
				var code = evt.which;
				
				// enter of splitchar should add an element
				if(code == 13 || String.fromCharCode(code) == options.splitChar) {
					// prevent default behaviour
					evt.preventDefault();
					evt.stopPropagation();
					
					// add element
					add();
				}

				// disable or enable button
				if($(this).val().replace(/^\s+|\s+$/g, '') == '') {
					blockSubmit = false;
					$('#addButton-'+ id).addClass('disabledButton');
				}
				else $('#addButton-'+ id).removeClass('disabledButton');
			});
			
			// bind click on add-button
			$('#addButton-'+ id).bind('click', function(evt) {
				// dont submit
				evt.preventDefault();
				evt.stopPropagation();
				
				// add element
				add();
			});
			
			// bind click on delete-button
			$('.deleteButton-'+ id).live('click', function(evt) {
				// dont submit
				evt.preventDefault();
				evt.stopPropagation();

				// remove element
				remove($(this).attr('rel'));
			});
			
			// add an element
			function add() {
				blockSubmit = false;
				// init some vars
				var value = $('#addValue-'+ id).val().replace(/^\s+|\s+$/g, '');
				var inElements = false;

				// reset box
				$('#addValue-'+ id).val('').focus();
				$('#addButton-'+ id).addClass('disabledButton');
				
				// only add new element if it isn't empty
				if(value != '') {
					// already in elements?
					for(var i in elements) if(value == elements[i]) inElements = true;
					
					// only add if not already in elements
					if(!inElements) {
						// add elements
						elements.push(value);
	
						// set new value
						$('#'+ id).val(elements.join(options.splitChar));
	
						// rebuild element list
						build();
					}
				}
			}
			
			// build the list
			function build() {
				// init var
				var html = '';
				
				// no items and message given?
				if(elements.length == 0 && options.emptyMessage != '') html = '<p class="helpTxt">'+ options.emptyMessage +'</p>';
				
				// items available
				else {
					// start html
					html = 	'<ul>';
					
					// loop elements
					for(var i in elements) {
						html +=	'	<li><span><strong>'+ elements[i] +'</strong>'+
								'		<a href="#" class="deleteButton-'+ id +'" rel="'+ elements[i] +'" title="'+ options.removeLabel +'">'+ options.removeLabel +'</a></span>'+
								'	</li>';
					}
					
					// end html
					html += '</ul>';
				}
				
				// set html
				$('#elementList-'+ id).html(html);
			}	
			
			// get all items
			function get() {
				// get chunks
				var chunks = $('#'+ id).val().split(options.splitChar);
				var elements = [];
				
				// loop elements and trim them from spaces
				for(var i in chunks) {
					value = chunks[i].replace(/^\s+|\s+$/g,'');
					if(value != '') elements.push(value);
				}
				
				return elements;
			}

			// remove an item
			function remove(value) {
				// get index for element
				var index = elements.indexOf(value);
				
				// remove element
				if(index > -1) elements.splice(index, 1);

				// set new value
				$('#'+ id).val(elements.join(options.splitChar));
				
				// rebuild element list
				build();
			}
		});
	};
})(jQuery);


/**
 * Multiple text box
 * 
 * @author Tijs Verkoyen <tijs@netlash.com>
 */
(function($) {
	$.fn.multipleTextBox = function(options) {
		// define defaults
		var defaults = {
			splitChar: ',',
			emptyMessage: '',
			addLabel: 'add',
			removeLabel: 'delete',
			autoCompleteUrl: '',
			canAddNew: false,
			showIconOnly: false
		};
		
		// extend options
		var options = $.extend(defaults, options);
	
		// loop all elements
		return this.each(function() {
			// define some vars
			var id = $(this).attr('id');
			var elements = get();
			var blockSubmit = false;

			// bind submit
			$(this.form).submit(function() { return !blockSubmit; });
			
			// remove previous HTML
			if($('#elementList-'+ id).length > 0){ $('#elementList-'+ id).parent('.multipleTextWrapper').remove(); }
			
			// build replace html
			var html = 	'<div class="multipleTextWrapper">'+
						'	<div id="elementList-'+ id +'" class="multipleTextList">'+
						'	</div>'+
						'	<div class="oneLiner">'+
						'		<p><input class="inputText dontSubmit" id="addValue-'+ id +'" name="addValue-'+ id +'" type="text" /></p>'+
						'		<div class="buttonHolder">'+
						'			<a href="#" id="addButton-'+ id +'" class="button icon iconAdd disabledButton';
			if(options.showIconOnly) html += ' iconOnly';
			html +=		'">'+
						'				<span>'+ options.addLabel +'</span>'+
						'			</a>'+
						'		</div>'+
						'	</div>'+
						'</div>';
			
			// hide current element
			$(this).css('visibility', 'hidden')
					.css('position', 'absolute')
					.css('top', '-9000px')
					.css('left', '-9000px')
					.attr('tabindex', '-1');
			
			// prepend html
			$(this).before(html);

			// add elements list
			build();
			
			// bind autocomplete if needed
			if(options.autoCompleteUrl != '') {
				$('#addValue-'+ id).autocomplete({
						delay: 200,
						minLength: 2,
						source: function(request, response) {
							$.ajax({ url: options.autoCompleteUrl, type: 'GET',
								data: 'term=' + request.term,
								success: function(data, textStatus) {
									// init var
									var realData = [];

									// alert the user
									if(data.code != 200 && jsBackend.debug) { alert(data.message); }
									if(data.code == 200) {
										for(var i in data.data) realData.push({ label: data.data[i].name, value: data.data[i].name });
									}
									
									// set response
									response(realData);
								}
							});
						}
					});
			}
			
			// bind keypress on value-field
			$('#addValue-'+ id).bind('keyup', function(evt) {
				blockSubmit = true;
				
				// grab code
				var code = evt.which;
				
				// enter of splitchar should add an element
				if(code == 13 || String.fromCharCode(code) == options.splitChar) {
					// prevent default behaviour
					evt.preventDefault();
					evt.stopPropagation();
					
					// add element
					add();
				}

				// disable or enable button
				if($(this).val().replace(/^\s+|\s+$/g, '') == '') {
					blockSubmit = false;
					$('#addButton-'+ id).addClass('disabledButton');
				}
				else $('#addButton-'+ id).removeClass('disabledButton');
			});
			
			// bind click on add-button
			$('#addButton-'+ id).bind('click', function(evt) {
				// dont submit
				evt.preventDefault();
				evt.stopPropagation();
				
				// add element
				add();
			});
			
			// bind click on delete-button
			$('.deleteButton-'+ id).live('click', function(evt) {
				// dont submit
				evt.preventDefault();
				evt.stopPropagation();

				// remove element
				remove($(this).attr('rel'));
			});
			
			// bind blur event on inputfields
			$('.inputField-'+ id).live('blur', function(evt) {
				// clear elements
				elements = [];

				// loop
				$('.inputField-'+ id).each(function() {
					// cleanup
					var value = $(this).val().replace(/^\s+|\s+$/g, '');
					
					// empty elements shouldn't be added
					if(value == '') { $(this).parent().parent().remove(); }
					
					// add
					else elements.push(value); 
				});

				// set new value
				$('#'+ id).val(elements.join(options.splitChar));
			});

			// add an element
			function add() {
				blockSubmit = false;
				// init some vars
				var value = $('#addValue-'+ id).val().replace(/^\s+|\s+$/g, '');
				var inElements = false;

				// reset box
				$('#addValue-'+ id).val('').focus();
				$('#addButton-'+ id).addClass('disabledButton');
				
				// only add new element if it isn't empty
				if(value != '') {
					// already in elements?
					for(var i in elements) if(value == elements[i]) inElements = true;
					
					// only add if not already in elements
					if(!inElements) {
						// add elements
						elements.push(value);
	
						// set new value
						$('#'+ id).val(elements.join(options.splitChar));
	
						// rebuild element list
						build();
					}
				}
			}
			
			// build the list
			function build() {
				// init var
				var html = '';
				
				// no items and message given?
				if(elements.length == 0 && options.emptyMessage != '') html = '<p class="helpTxt">'+ options.emptyMessage +'</p>';
				
				// items available
				else {
					// start html
					html = 	'<ul>';
					
					// loop elements
					for(var i in elements) {
						html +=	'	<li class="oneLiner">'+
								'		<p><input class="inputText dontSubmit inputField-'+ id +'" name="inputField-'+ id +'[]" type="text" value="'+ elements[i] +'" /></p>'+
								'		<div class="buttonHolder">'+
								'			<a href="#" class="button icon iconDelete iconOnly deleteButton-'+ id +'" rel="'+ elements[i] +'" title="'+ options.removeLabel +'"><span>'+ options.removeLabel +'</span></a>'+
								'		</div>'+
								'	</li>';
					}
					
					// end html
					html += '</ul>';
				}
				
				// set html
				$('#elementList-'+ id).html(html);
			}	
			
			// get all items
			function get() {
				// get chunks
				var chunks = $('#'+ id).val().split(options.splitChar);
				var elements = [];
				
				// loop elements and trim them from spaces
				for(var i in chunks) {
					value = chunks[i].replace(/^\s+|\s+$/g,'');
					if(value != '') elements.push(value);
				}
				
				return elements;
			}

			// remove an item
			function remove(value) {
				// get index for element
				var index = elements.indexOf(value);
				
				// remove element
				if(index > -1) elements.splice(index, 1);

				// set new value
				$('#'+ id).val(elements.join(options.splitChar));
				
				// rebuild element list
				build();
			}
		});
	};
})(jQuery);